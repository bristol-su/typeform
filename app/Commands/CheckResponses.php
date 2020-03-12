<?php

namespace BristolSU\Module\Typeform\Commands;

use BristolSU\Module\Typeform\Jobs\UpdateResponses;
use BristolSU\Support\ModuleInstance\Contracts\ModuleInstanceRepository;
use BristolSU\Support\ModuleInstance\ModuleInstance;
use Illuminate\Console\Command;

class CheckResponses extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'typeform:responses 
                            {moduleinstance? : The ID of the module instance. Leave blank to run for all module instances}';

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'typeform:responses';

    /**
     * The console command description.
     *
     * @var string|null
     */
    protected $description = 'Updates the responses for a module instance or multiple module instances.';

    public function handle()
    {
        foreach($this->moduleInstances() as $moduleInstance) {
            dispatch(new UpdateResponses($moduleInstance));
        }
    }

    public function moduleInstances()
    {
        $id = $this->argument('moduleinstance');
        if($id === null) {
            return collect(app(ModuleInstanceRepository::class)->allWithAlias('typeform'))->filter(function(ModuleInstance $moduleInstance) {
                return $moduleInstance->setting('collect_responses', false)
                    && !$moduleInstance->setting('use_webhook', true)
                    && $moduleInstance->setting('form_id');
            });
        }
        return [app(ModuleInstanceRepository::class)->getById($id)];
    }

}