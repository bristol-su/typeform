<?php

namespace BristolSU\Module\Typeform\Commands;

use BristolSU\Module\Typeform\Jobs\UpdateResponses;
use BristolSU\Module\Typeform\Models\Response;
use BristolSU\Module\Typeform\Models\Webhook;
use BristolSU\Module\Typeform\Typeform\Client;
use BristolSU\Module\Typeform\Typeform\ResponseHandler;
use BristolSU\Module\Typeform\Typeform\ResponsePayload;
use BristolSU\Support\ModuleInstance\Contracts\Connection\ModuleInstanceServiceRepository;
use BristolSU\Support\ModuleInstance\Contracts\ModuleInstanceRepository;
use BristolSU\Support\ModuleInstance\ModuleInstance;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;

class CheckResponses extends Command
{
// TODO Move the bulk of the logic to a job
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
    protected $description = 'Gets all responses and saves any that are not currently saved.';

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
            return collect(app(ModuleInstanceRepository::class)->all())->filter(function($moduleInstance) {
                try {
                    return $moduleInstance->moduleInstanceSettings()->where('key', 'collect_responses')->firstOrFail()->value
                        && !$moduleInstance->moduleInstanceSettings()->where('key', 'use_webhook')->firstOrFail()->value
                        && !is_null($moduleInstance->moduleInstanceSettings()->where('key', 'form_id')->firstOrFail()->value);
                } catch (ModelNotFoundException $e) {
                    return false;
                }
            });
        }
        return [app(ModuleInstanceRepository::class)->getById($id)];
    }

}