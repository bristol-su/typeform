<?php

namespace BristolSU\Module\Typeform\Http\Controllers\Webhook;

use BristolSU\Module\Typeform\Http\Controllers\Controller;
use BristolSU\Module\Typeform\Typeform\Handler\ResponseHandler;
use BristolSU\Module\Typeform\Typeform\Handler\WebhookPayload;
use Illuminate\Http\Request;

class IncomingWebhookController extends Controller
{

    public function store(Request $request, ResponseHandler $handler)
    {
        $payload = new WebhookPayload($request->all());
        $response = $handler->handle($payload);
        
        return $response->load(['answers', 'answers.field']);
    }

    
}