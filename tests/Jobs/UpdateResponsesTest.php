<?php

namespace BristolSU\Module\Tests\Typeform\Jobs;

use BristolSU\Module\Tests\Typeform\TestCase;
use BristolSU\Module\Typeform\Jobs\UpdateResponses;
use BristolSU\Module\Typeform\Models\Response;
use BristolSU\Module\Typeform\Models\Webhook;
use BristolSU\Module\Typeform\Typeform\Client;
use BristolSU\Module\Typeform\Typeform\Handler\ResponseHandler;
use BristolSU\Module\Typeform\Typeform\Handler\ResponsePayload;
use BristolSU\Support\Connection\Contracts\Connector;
use BristolSU\Support\ModuleInstance\Connection\NoConnectionAvailable;
use BristolSU\Support\ModuleInstance\Contracts\Connection\ModuleInstanceServiceRepository;
use BristolSU\Support\ModuleInstance\ModuleInstance;
use BristolSU\Support\ModuleInstance\Settings\ModuleInstanceSetting;
use Prophecy\Argument;

class UpdateResponsesTest extends TestCase
{

    /** @test */
    public function it_returns_null_if_no_connection_available_exception_thrown(){
        $moduleInstance = factory(ModuleInstance::class)->create();

        $moduleInstanceServiceRepository = $this->prophesize(ModuleInstanceServiceRepository::class);
        $connector = $this->prophesize(Connector::class);
        $moduleInstanceServiceRepository->getConnectorForService('typeform', $moduleInstance->id)
            ->shouldBeCalled()->willThrow(new NoConnectionAvailable('No connection has been found for Typeform'));
        $this->instance(ModuleInstanceServiceRepository::class, $moduleInstanceServiceRepository->reveal());

        $job = new UpdateResponses($moduleInstance);
        $this->assertNull(
            $job->handle()
        );
    }
    
    /** @test */
    public function it_returns_null_if_the_form_id_is_null(){
        ModuleInstanceSetting::create(['module_instance_id' => $this->getModuleInstance()->id, 'key' => 'collect_responses', 'value' => true]);
        ModuleInstanceSetting::create(['module_instance_id' => $this->getModuleInstance()->id, 'key' => 'use_webhook', 'value' => true]);

        $webhook = factory(Webhook::class)->create([
            'module_instance_id' => $this->getModuleInstance()->id(),
            'tag' => Webhook::generatedTag($this->getModuleInstance()),
            'form_id' => 'a-form-id'
        ]);

        $client = $this->prophesize(Client::class);

        $job = new DummyUpdateResponsesJob($this->getModuleInstance());
        $job->setClient($client->reveal());
        $this->assertNull(
            $job->handle()
        );
    }
    
    /** @test */
    public function it_handles_all_responses_which_have_not_been_handled_and_have_the_right_module_instance(){
        $otherModuleInstance = factory(ModuleInstance::class)->create();
        
        ModuleInstanceSetting::create(['module_instance_id' => $this->getModuleInstance()->id, 'key' => 'collect_responses', 'value' => true]);
        ModuleInstanceSetting::create(['module_instance_id' => $this->getModuleInstance()->id, 'key' => 'use_webhook', 'value' => true]);
        ModuleInstanceSetting::create(['module_instance_id' => $this->getModuleInstance()->id, 'key' => 'form_id', 'value' => 'form-123']);

        $webhook = factory(Webhook::class)->create([
            'module_instance_id' => $this->getModuleInstance()->id(),
            'tag' => Webhook::generatedTag($this->getModuleInstance()),
            'form_id' => 'a-form-id'
        ]);

        $client = $this->prophesize(Client::class);
        $client->allResponses('form-123')->shouldBeCalled()->willReturn([
            [
                'hidden' => ['module_instance' => $this->getModuleInstance()->id()],
                'token' => '123'
            ],
            [
                'hidden' => ['module_instance' => $this->getModuleInstance()->id()],
                'token' => '1234'
            ],
            [
                'hidden' => ['module_instance' => $this->getModuleInstance()->id()],
                'token' => '12345'
            ],
            [
                'hidden' => ['module_instance' => $otherModuleInstance->id()],
                'token' => '123456'
            ],
            [
                'hidden' => ['module_instance' => $this->getModuleInstance()->id()],
                'token' => '1234567'
            ],
            [
                'token' => '12345678'
            ],
        ]);
        factory(Response::class)->create(['id' => '1234567']);
        $client->allFields('form-123')->shouldBeCalled()->willReturn([]);
        
        $handler = $this->prophesize(ResponseHandler::class);
        $handler->handle(Argument::that(function($arg) {
            return $arg instanceof ResponsePayload && $arg->responseId() === '123';
        }))->shouldBeCalled();
        $handler->handle(Argument::that(function($arg) {
            return $arg instanceof ResponsePayload && $arg->responseId() === '1234';
        }))->shouldBeCalled();
        $handler->handle(Argument::that(function($arg) {
            return $arg instanceof ResponsePayload && $arg->responseId() === '12345';
        }))->shouldBeCalled();
        $handler->handle(Argument::that(function($arg) {
            return $arg instanceof ResponsePayload && $arg->responseId() === '123456';
        }))->shouldNotBeCalled();
        $handler->handle(Argument::that(function($arg) {
            return $arg instanceof ResponsePayload && $arg->responseId() === '1234567';
        }))->shouldNotBeCalled();
        $handler->handle(Argument::that(function($arg) {
            return $arg instanceof ResponsePayload && $arg->responseId() === '12345678';
        }))->shouldNotBeCalled();
        $this->instance(ResponseHandler::class, $handler->reveal());
        
        $job = new DummyUpdateResponsesJob($this->getModuleInstance());
        $job->setClient($client->reveal());
        $job->handle();
    }
    
}

class DummyUpdateResponsesJob extends UpdateResponses {

    protected $client;
    
    public function setClient(Client $client)
    {
        $this->client = $client;
    }

    public function resolveClient()
    {
        return ($this->client??parent::resolveClient());
    }
}