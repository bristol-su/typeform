<?php

namespace BristolSU\Module\Typeform\Http\Controllers\Webhook;

use BristolSU\Module\Typeform\Http\Controllers\Controller;
use BristolSU\Module\Typeform\Typeform\Handler\ResponseHandler;
use BristolSU\Module\Typeform\Typeform\Handler\WebhookPayload;
use BristolSU\Support\ModuleInstance\ModuleInstance;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class IncomingWebhookController extends Controller
{

    public function store(Request $request, ResponseHandler $handler)
    {
        $payload = new WebhookPayload($request->all());
        if($payload->moduleInstanceId() === app(ModuleInstance::class)->id()) {
            $response = $handler->handle($payload);
            return $response->load(['answers', 'answers.field']);
        }
        
        return Response::create('Not Found', 404);
    }

    
}