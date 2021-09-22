<?php

namespace BristolSU\Module\Tests\Typeform\Typeform;

use BristolSU\Module\Tests\Typeform\TestCase;
use BristolSU\Module\Typeform\Models\Webhook;
use BristolSU\Module\Typeform\Typeform\Client;
use BristolSU\Support\Connection\Contracts\Connector;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;

class ClientTest extends TestCase
{

    /** @test */
    public function webhookExists_returns_true_if_the_webhook_exists()
    {
        $connector = $this->prophesize(Connector::class);
        $connector->request('GET', 'https://api.typeform.com/forms/form-id-123/webhooks/webhook-tag-123')->shouldBeCalled()
            ->willReturn(new Response(200, [], json_encode([
                'id' => 'yRtagDm8AT',
                'form_id' => 'form-id-123',
                'tag' => 'webhook-tag-123',
                'url' => 'https://test.com',
                'enabled' => true,
                'verify_ssl' => true,
                'created_at' => '2016-11-21T12:23:28Z',
                'updated_at' => '2016-11-21T12:23:28Z'
            ])));

        $webhook = Webhook::factory()->create([
            'form_id' => 'form-id-123',
            'tag' => 'webhook-tag-123'
        ]);

        $client = new Client($connector->reveal());
        $this->assertTrue($client->webhookExists($webhook));
    }

    /** @test */
    public function webhookExists_returns_true_if_the_webhook_does_not_exist()
    {
        $connector = $this->prophesize(Connector::class);
        $connector->request('GET', 'https://api.typeform.com/forms/form-id-123/webhooks/webhook-tag-123')->shouldBeCalled()
            ->willThrow(
                new ClientException('Webhook not found',
                    new Request('GET', 'https//api.typeform.com/forms/form-id-123/webhooks/webhook-tag-123'),
                    new Response(404)
                )
            );

        $webhook = Webhook::factory()->create([
            'form_id' => 'form-id-123',
            'tag' => 'webhook-tag-123'
        ]);

        $client = new Client($connector->reveal());
        $this->assertFalse($client->webhookExists($webhook));
    }

    /** @test */
    public function webhookExists_throws_an_exception_if_the_response_code_is_not_a_404(){
        $this->expectException(ClientException::class);
        $this->expectExceptionMessage('Unauthorized');

        $connector = $this->prophesize(Connector::class);
        $connector->request('GET', 'https://api.typeform.com/forms/form-id-123/webhooks/webhook-tag-123')->shouldBeCalled()
            ->willThrow(
                new ClientException('Unauthorized',
                    new Request('GET', 'https//api.typeform.com/forms/form-id-123/webhooks/webhook-tag-123'),
                    new Response(403)
                )
            );

        $webhook = Webhook::factory()->create([
            'form_id' => 'form-id-123',
            'tag' => 'webhook-tag-123'
        ]);

        $client = new Client($connector->reveal());
        $client->webhookExists($webhook);
    }

    /** @test */
    public function webhookEnabled_returns_true_if_the_webhook_is_enabled(){
        $connector = $this->prophesize(Connector::class);
        $connector->request('GET', 'https://api.typeform.com/forms/form-id-123/webhooks/webhook-tag-123')->shouldBeCalled()
            ->willReturn(new Response(200, [], json_encode([
                'id' => 'yRtagDm8AT',
                'form_id' => 'form-id-123',
                'tag' => 'webhook-tag-123',
                'url' => 'https://test.com',
                'enabled' => true,
                'verify_ssl' => true,
                'created_at' => '2016-11-21T12:23:28Z',
                'updated_at' => '2016-11-21T12:23:28Z'
            ])));

        $webhook = Webhook::factory()->create([
            'form_id' => 'form-id-123',
            'tag' => 'webhook-tag-123'
        ]);

        $client = new Client($connector->reveal());
        $this->assertTrue($client->webhookEnabled($webhook));
    }

    /** @test */
    public function webhookEnabled_returns_false_if_the_webhook_is_not_enabled(){
        $connector = $this->prophesize(Connector::class);
        $connector->request('GET', 'https://api.typeform.com/forms/form-id-123/webhooks/webhook-tag-123')->shouldBeCalled()
            ->willReturn(new Response(200, [], json_encode([
                'id' => 'yRtagDm8AT',
                'form_id' => 'form-id-123',
                'tag' => 'webhook-tag-123',
                'url' => 'https://test.com',
                'enabled' => false,
                'verify_ssl' => true,
                'created_at' => '2016-11-21T12:23:28Z',
                'updated_at' => '2016-11-21T12:23:28Z'
            ])));

        $webhook = Webhook::factory()->create([
            'form_id' => 'form-id-123',
            'tag' => 'webhook-tag-123'
        ]);

        $client = new Client($connector->reveal());
        $this->assertFalse($client->webhookEnabled($webhook));
    }

    /** @test */
    public function webhookEnabled_throws_an_exception_if_guzzle_throws_an_exception(){
        $this->expectException(ClientException::class);
        $this->expectExceptionMessage('Webhook not found');

        $connector = $this->prophesize(Connector::class);
        $connector->request('GET', 'https://api.typeform.com/forms/form-id-123/webhooks/webhook-tag-123')->shouldBeCalled()
            ->willThrow(
                new ClientException('Webhook not found',
                    new Request('GET', 'https//api.typeform.com/forms/form-id-123/webhooks/webhook-tag-123'),
                    new Response(404)
                )
            );

        $webhook = Webhook::factory()->create([
            'form_id' => 'form-id-123',
            'tag' => 'webhook-tag-123'
        ]);

        $client = new Client($connector->reveal());
        $client->webhookEnabled($webhook);
    }

    /** @test */
    public function webhoookCreate_creates_a_webhook(){
        $webhook = Webhook::factory()->create([
            'form_id' => 'form-id-123',
            'tag' => 'webhook-tag-123'
        ]);

        $connector = $this->prophesize(Connector::class);
        $connector->request('PUT', 'https://api.typeform.com/forms/form-id-123/webhooks/webhook-tag-123', [
            'json' => ['url' => $webhook->url(), 'enabled' => true]
        ])->shouldBeCalled()
            ->willReturn(new Response(200, [], json_encode([
                'id' => 'yRtagDm8AT',
                'form_id' => 'form-id-123',
                'tag' => 'webhook-tag-123',
                'url' => $webhook->url(),
                'enabled' => false,
                'verify_ssl' => true,
                'created_at' => '2016-11-21T12:23:28Z',
                'updated_at' => '2016-11-21T12:23:28Z'
            ])));

        $client = new Client($connector->reveal());
        $client->webhookCreate($webhook);
    }

    /** @test */
    public function webhoookDelete_deletes_a_webhook(){
        $webhook = Webhook::factory()->create([
            'form_id' => 'form-id-123',
            'tag' => 'webhook-tag-123'
        ]);

        $connector = $this->prophesize(Connector::class);
        $connector->request('DELETE', 'https://api.typeform.com/forms/form-id-123/webhooks/webhook-tag-123')->shouldBeCalled()
            ->willReturn(new Response(204));

        $client = new Client($connector->reveal());
        $client->webhookDelete($webhook);
    }

    /** @test */
    public function webhoookEnable_enables_a_webhook(){
        $webhook = Webhook::factory()->create([
            'form_id' => 'form-id-123',
            'tag' => 'webhook-tag-123'
        ]);

        $connector = $this->prophesize(Connector::class);
        $connector->request('PUT', 'https://api.typeform.com/forms/form-id-123/webhooks/webhook-tag-123', [
            'json' => ['enabled' => true]
        ])->shouldBeCalled()
            ->willReturn(new Response(200, [], json_encode([
                'id' => 'yRtagDm8AT',
                'form_id' => 'form-id-123',
                'tag' => 'webhook-tag-123',
                'url' => $webhook->url(),
                'enabled' => true,
                'verify_ssl' => true,
                'created_at' => '2016-11-21T12:23:28Z',
                'updated_at' => '2016-11-21T12:23:28Z'
            ])));

        $client = new Client($connector->reveal());
        $client->webhookEnable($webhook);
    }

    /** @test */
    public function webhoookDisable_disables_a_webhook(){
        $webhook = Webhook::factory()->create([
            'form_id' => 'form-id-123',
            'tag' => 'webhook-tag-123'
        ]);

        $connector = $this->prophesize(Connector::class);
        $connector->request('PUT', 'https://api.typeform.com/forms/form-id-123/webhooks/webhook-tag-123', [
            'json' => ['enabled' => false]
        ])->shouldBeCalled()
            ->willReturn(new Response(200, [], json_encode([
                'id' => 'yRtagDm8AT',
                'form_id' => 'form-id-123',
                'tag' => 'webhook-tag-123',
                'url' => $webhook->url(),
                'enabled' => false,
                'verify_ssl' => true,
                'created_at' => '2016-11-21T12:23:28Z',
                'updated_at' => '2016-11-21T12:23:28Z'
            ])));

        $client = new Client($connector->reveal());
        $client->webhookDisable($webhook);
    }

    /** @test */
    public function allResponses_returns_all_the_responses(){
        $connector = $this->prophesize(Connector::class);
        $connector->request('GET', 'https://api.typeform.com/forms/form-id-123/responses', [
            'query' => ['page_size' => 1000, 'completed' => true]
        ])->shouldBeCalled()
            ->willReturn(new Response(200, [], json_encode([
                'items' => [
                    ['token' => 'test1'],
                    ['token' => 'test2'],
                ]
            ])));

        $client = new Client($connector->reveal());
        $responses = $client->allResponses('form-id-123');
        $this->assertEquals([
            ['token' => 'test1'],
            ['token' => 'test2']
        ], $responses);
    }

    /** @test */
    public function allFields_returns_all_the_fields(){
        $connector = $this->prophesize(Connector::class);
        $connector->request('GET', 'https://api.typeform.com/forms/form-id-123')->shouldBeCalled()
            ->willReturn(new Response(200, [], json_encode([
                'fields' => [
                    ['field1' => 'test1'],
                    ['field2' => 'test2'],
                ]
            ])));

        $client = new Client($connector->reveal());
        $fields = $client->allFields('form-id-123');
        $this->assertEquals([
            ['field1' => 'test1'],
            ['field2' => 'test2']
        ], $fields);
    }
}
