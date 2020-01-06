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

    public function handle()
    {
        foreach ($this->moduleInstances() as $moduleInstance) {
            if(! in_array('typeform', array_merge(
                $serviceRequest->getRequired($moduleInstance->alias()), $serviceRequest->getOptional($moduleInstance->alias())
            ))) {
                continue;
            }
            $client = app(Client::class, ['connector' => app(ModuleInstanceServiceRepository::class)->getConnectorForService('typeform', $moduleInstance->id)]);
            if($this->usesWebhook($moduleInstance)) {
                $webhook = $this->getWebhook($moduleInstance);
                if(! $client->webhookExists($webhook)) {
                    $client->webhookCreate($webhook);
                }
                if(!$client->webhookEnabled($webhook)) {
                    $client->webhookEnable($webhook);
                }
            } elseif(Webhook::fromModuleInstance($moduleInstance)->count() > 0){
                $webhook = $this->getWebhook($moduleInstance);
                if($client->webhookExists($webhook) && $client->webhookEnabled($webhook)) {
                    $client->webhookDisable($webhook);
                }
//                if($client->webhookExists($webhook)) {
//                    $client->webhookDelete($webhook);
//                }
            }
        }
    }

    public function moduleInstances()
    {
        $id = $this->argument('moduleinstance');
        return ($id === null
            ? app(ModuleInstanceRepository::class)->all()
            : [app(ModuleInstanceRepository::class)->getById($id)]);
    }

    public function getWebhook(ModuleInstance $moduleInstance)
    {
        try {
            return Webhook::fromModuleInstance($moduleInstance)->firstOrFail();
        } catch (ModelNotFoundException $e) {
            return Webhook::create([
                'module_instance_id' => $moduleInstance->id,
                'tag' => Webhook::generatedTag($moduleInstance),
                'form_id' => $moduleInstance->moduleInstanceSettings()->where('key', 'form_id')->firstOrFail()->value
            ]);
        }
    }

    public function usesWebhook(ModuleInstance $moduleInstance)
    {
        try {
            return $moduleInstance->moduleInstanceSettings()->where('key', 'collect_responses')->firstOrFail()->value
                && $moduleInstance->moduleInstanceSettings()->where('key', 'use_webhook')->firstOrFail()->value;
        } catch (ModelNotFoundException $e) {
            return false;
        }
    }
}
