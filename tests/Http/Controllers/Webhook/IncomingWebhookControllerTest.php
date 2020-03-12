<?php

namespace BristolSU\Module\Tests\Typeform\Http\Controllers\Webhook;

use BristolSU\Module\Tests\Typeform\TestCase;
use BristolSU\Module\Typeform\Models\Response;
use BristolSU\Module\Typeform\Typeform\Handler\ResponseHandler;
use BristolSU\Module\Typeform\Typeform\Handler\WebhookPayload;
use Prophecy\Argument;

class IncomingWebhookControllerTest extends TestCase
{

    /** @test */
    public function it_creates_a_payload_and_passes_it_to_the_handler(){
        $bundle = [
            'form_response' => [
                'token' => 'response_id'
            ]
        ];
        
        $formResponse = factory(Response::class)->create();
        
        $handler = $this->prophesize(ResponseHandler::class);
        $handler->handle(Argument::that(function($arg) {
            return $arg instanceof WebhookPayload && $arg->responseId() === 'response_id';
        }))->shouldBeCalled()->willReturn($formResponse);
        $this->instance(ResponseHandler::class, $handler->reveal());
        
        $response = $this->post($this->adminApiUrl('webhook/responses'), $bundle);
        $response->assertStatus(201);
        
    }
    
}