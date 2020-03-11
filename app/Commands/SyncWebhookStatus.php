<?php

namespace BristolSU\Module\Typeform\Commands;

use BristolSU\Module\Typeform\Models\Webhook;
use BristolSU\Module\Typeform\Typeform\Client;
use BristolSU\Support\ModuleInstance\Contracts\Connection\ModuleInstanceServiceRepository;
use BristolSU\Support\ModuleInstance\Contracts\ModuleInstanceRepository;
use BristolSU\Support\ModuleInstance\ModuleInstance;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class SyncWebhookStatus extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'typeform:webhook 
                            {moduleinstance? : The ID of the module instance. Leave blank to run for all module instances}';

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'typeform:webhook';

    /**
     * The console command description.
     *
     * @var string|null
     */
    protected $description = 'Syncronise the webhook settings with Typeform for a module instance. Creates and removes webhooks.';

    /**
     * Syncronise the webhook status for each of the module instances requested
     */
    public function handle()
    {
        foreach ($this->moduleInstances() as $moduleInstance) {
            dispatch(new \BristolSU\Module\Typeform\Jobs\SyncWebhookStatus($moduleInstance));
        }
    }

    /**
     * Get the module instances to syncronise
     * 
     * @return array|ModuleInstance[]
     */
    private function moduleInstances()
    {
        $id = $this->argument('moduleinstance');
        return ($id === null
            ? app(ModuleInstanceRepository::class)->all()
            : [app(ModuleInstanceRepository::class)->getById($id)]);
    }

}
