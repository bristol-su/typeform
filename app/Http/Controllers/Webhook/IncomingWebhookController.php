<?php

namespace BristolSU\Module\Typeform\Http\Controllers\Webhook;

use BristolSU\Module\Typeform\Http\Controllers\Controller;
use BristolSU\Module\Typeform\Typeform\ResponseHandler;
use BristolSU\Module\Typeform\Typeform\WebhookPayload;
use Illuminate\Http\Request;

class IncomingWebhookController extends Controller
{

    // TODO Secure webhook
    public function store(Request $request, ResponseHandler $handler)
    {
        $payload = new WebhookPayload($request->all());
        $response = $handler->handle($request->route('module_instance_slug'), $payload);
        
        return $response->load(['answers', 'answers.field']);
    }

    
}