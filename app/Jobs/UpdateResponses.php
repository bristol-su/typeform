<?php

namespace BristolSU\Module\Typeform\Jobs;

use BristolSU\Module\Typeform\Models\Response;
use BristolSU\Module\Typeform\Typeform\Client;
use BristolSU\Module\Typeform\Typeform\Handler\ResponseHandler;
use BristolSU\Module\Typeform\Typeform\Handler\ResponsePayload;
use BristolSU\Support\ModuleInstance\Contracts\Connection\ModuleInstanceServiceRepository;
use BristolSU\Support\ModuleInstance\ModuleInstance;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UpdateResponses implements ShouldQueue
{
    
    use Dispatchable, Queueable, SerializesModels;

    /**
     * @var ModuleInstance
     */
    private $moduleInstance;

    public function __construct(ModuleInstance $moduleInstance)
    {
        $this->moduleInstance = $moduleInstance;
    }

    public function handle()
    {
        $client = app(Client::class,
            ['connector' => app(ModuleInstanceServiceRepository::class)->getConnectorForService('typeform', $this->moduleInstance->id)]
        );
        
        $formId = $this->moduleInstance->setting('form_id');
        if($formId === null) {
            return null;
        }
        
        $responses = collect($client->allResponses($formId))->filter(function($response) {
            if(isset($response['hidden']) && isset($response['hidden']['module_instance'])) {
                return Response::where('id', $response['token'])->count() === 0
                    && $response['hidden']['module_instance'] == $this->moduleInstance->id();
            }
            return false;
        })->values();

        foreach($responses as $response) {
            $payload = new ResponsePayload($response, $formId, $client->allFields($formId));
            (new ResponseHandler())->handle($payload);
        }
    }

}