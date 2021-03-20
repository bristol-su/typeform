<?php

namespace BristolSU\Module\Typeform\Jobs;

use BristolSU\Module\Typeform\Models\Response;
use BristolSU\Module\Typeform\Typeform\Client;
use BristolSU\Module\Typeform\Typeform\Handler\ResponseHandler;
use BristolSU\Module\Typeform\Typeform\Handler\ResponsePayload;
use BristolSU\Support\ModuleInstance\Connection\NoConnectionAvailable;
use BristolSU\Support\ModuleInstance\Contracts\Connection\ModuleInstanceServiceRepository;
use BristolSU\Support\ModuleInstance\ModuleInstance;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Spatie\RateLimitedMiddleware\RateLimited;

class UpdateResponses implements ShouldQueue
{

    use Dispatchable, Queueable, SerializesModels, InteractsWithQueue;

    /**
     * @var ModuleInstance
     */
    public $moduleInstance;

    public function __construct(ModuleInstance $moduleInstance)
    {
        $this->moduleInstance = $moduleInstance;
    }

    public function middleware()
    {
        $rateLimitedMiddleware = (new RateLimited())
            ->key('typeform')
            ->allow(1)
            ->everySeconds(1)
            ->releaseAfterSeconds(3);

        return [$rateLimitedMiddleware];
    }

    public function handle()
    {
        if(!app(ModuleInstance::class)->exists) {
            app()->instance(ModuleInstance::class, $this->moduleInstance);
        }
        try {
            $client = $this->resolveClient();
        } catch (NoConnectionAvailable $e) {
            return;
        }

        $formId = $this->moduleInstance->setting('form_id');
        if ($formId === null) {
            return null;
        }

        $responses = collect($client->allResponses($formId))->filter(function ($response) {
            if (
                isset($response['hidden'])
                && isset($response['hidden']['module_instance'])
                && isset($response['hidden']['activity_instance'])
                && isset($response['hidden']['portal_user_id'])
            ) {
                return Response::where('id', $response['token'])->count() === 0
                    && (int) $response['hidden']['module_instance'] === $this->moduleInstance->id();
            }
            return false;
        })->values();

        $fields = $client->allFields($formId);

        foreach ($responses as $response) {
            $payload = new ResponsePayload($response, $formId, $fields);
            app(ResponseHandler::class)->handle($payload);
        }
    }

    public function retryUntil(): \DateTime
    {
        return now()->addMinutes(5);
    }

    /**
     * @return Client
     */
    protected function resolveClient()
    {
        return app(Client::class,
            ['connector' => app(ModuleInstanceServiceRepository::class)->getConnectorForService('typeform', $this->moduleInstance->id)]
        );
    }

}
