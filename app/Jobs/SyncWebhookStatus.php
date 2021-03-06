<?php

namespace BristolSU\Module\Typeform\Jobs;

use BristolSU\Module\Typeform\Models\Webhook;
use BristolSU\Module\Typeform\Typeform\Client;
use BristolSU\Support\ModuleInstance\Connection\NoConnectionAvailable;
use BristolSU\Support\ModuleInstance\Contracts\Connection\ModuleInstanceServiceRepository;
use BristolSU\Support\ModuleInstance\ModuleInstance;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SyncWebhookStatus implements ShouldQueue
{
    use Dispatchable, Queueable, SerializesModels;

    /**
     * @var ModuleInstance
     */
    public $moduleInstance;

    public function __construct(ModuleInstance $moduleInstance)
    {
        $this->moduleInstance = $moduleInstance;
    }

    public function handle()
    {
        try {
            $client = $this->resolveClient();
        } catch (NoConnectionAvailable $e) {
            return;
        }


        if(!$this->isReady()) {
            return;
        }

        // If the module should use a webhook, ensure it exists and is enabled
        if($this->usesWebhook()) {
            $webhook = $this->getWebhook();
            if(! $client->webhookExists($webhook)) {
                $client->webhookCreate($webhook);
            }
            if(!$client->webhookEnabled($webhook)) {
                $client->webhookEnable($webhook);
            }
        } elseif(Webhook::fromModuleInstance($this->moduleInstance)->count() > 0){
            $webhook = $this->getWebhook();
            if($client->webhookExists($webhook) && $client->webhookEnabled($webhook)) {
                $client->webhookDisable($webhook);
            }
        }

    }


    /**
     * Find or create a webhook for a module instance
     *
     * @return Webhook
     */
    private function getWebhook(): Webhook
    {
        try {
            return Webhook::fromModuleInstance($this->moduleInstance)->firstOrFail();
        } catch (ModelNotFoundException $e) {
            return Webhook::create([
                'module_instance_id' => $this->moduleInstance->id,
                'tag' => Webhook::generatedTag($this->moduleInstance),
                'form_id' => $this->moduleInstance->setting('form_id')
            ]);
        }
    }

    /**
     * Check if the module instance should use a webhook
     *
     * @return bool
     */
    private function usesWebhook(): bool
    {
        return $this->moduleInstance->setting('collect_responses', false)
            && $this->moduleInstance->setting('use_webhook', true);
    }

    protected function resolveClient()
    {
        return app(Client::class,
            ['connector' => app(ModuleInstanceServiceRepository::class)->getConnectorForService('typeform', $this->moduleInstance->id)]
        );
    }

    private function isReady()
    {
        return (bool) $this->moduleInstance->setting('form_id', null);
    }
}
