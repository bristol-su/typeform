<?php

namespace BristolSU\Module\Tests\Typeform\Jobs;

use BristolSU\Module\Tests\Typeform\TestCase;
use BristolSU\Module\Typeform\Jobs\SyncWebhookStatus;
use BristolSU\Module\Typeform\Models\Webhook;
use BristolSU\Module\Typeform\Typeform\Client;
use BristolSU\Support\Connection\Contracts\Connector;
use BristolSU\Support\ModuleInstance\Connection\NoConnectionAvailable;
use BristolSU\Support\ModuleInstance\Contracts\Connection\ModuleInstanceServiceRepository;
use BristolSU\Support\ModuleInstance\ModuleInstance;
use BristolSU\Support\ModuleInstance\Settings\ModuleInstanceSetting;
use Illuminate\Support\Facades\Bus;
use Prophecy\Argument;

class SyncWebhookStatusTest extends TestCase
{

    /** @test */
    public function it_returns_null_if_no_connection_available_exception_thrown(){
        $moduleInstance = ModuleInstance::factory()->create();

        $moduleInstanceServiceRepository = $this->prophesize(ModuleInstanceServiceRepository::class);
        $connector = $this->prophesize(Connector::class);
        $moduleInstanceServiceRepository->getConnectorForService('typeform', $moduleInstance->id)
            ->shouldBeCalled()->willThrow(new NoConnectionAvailable('No connection has been found for Typeform'));
        $this->instance(ModuleInstanceServiceRepository::class, $moduleInstanceServiceRepository->reveal());

        $job = new SyncWebhookStatus($moduleInstance);
        $this->assertNull(
            $job->handle()
        );
    }

    /** @test */
    public function it_returns_null_if_the_form_id_is_an_empty_string(){
        ModuleInstanceSetting::create(['module_instance_id' => $this->getModuleInstance()->id, 'key' => 'collect_responses', 'value' => true]);
        ModuleInstanceSetting::create(['module_instance_id' => $this->getModuleInstance()->id, 'key' => 'use_webhook', 'value' => true]);
        ModuleInstanceSetting::create(['module_instance_id' => $this->getModuleInstance()->id, 'key' => 'form_id', 'value' => '']);

        $job = new DummyWebhookJob($this->getModuleInstance());
        $this->assertNull(
            $job->handle()
        );
    }

    /** @test */
    public function it_returns_null_if_the_form_id_is_missing(){
        ModuleInstanceSetting::create(['module_instance_id' => $this->getModuleInstance()->id, 'key' => 'collect_responses', 'value' => true]);
        ModuleInstanceSetting::create(['module_instance_id' => $this->getModuleInstance()->id, 'key' => 'use_webhook', 'value' => true]);

        $job = new DummyWebhookJob($this->getModuleInstance());
        $this->assertNull(
            $job->handle()
        );
    }

    /** @test */
    public function it_checks_if_the_webhook_exists_and_is_enabled_if_the_webhook_should_be_used(){
        ModuleInstanceSetting::create(['module_instance_id' => $this->getModuleInstance()->id, 'key' => 'collect_responses', 'value' => true]);
        ModuleInstanceSetting::create(['module_instance_id' => $this->getModuleInstance()->id, 'key' => 'use_webhook', 'value' => true]);
        ModuleInstanceSetting::create(['module_instance_id' => $this->getModuleInstance()->id, 'key' => 'form_id', 'value' => 'a-form-id']);

        $webhook = Webhook::factory()->create([
            'module_instance_id' => $this->getModuleInstance()->id(),
            'tag' => Webhook::generatedTag($this->getModuleInstance()),
            'form_id' => 'a-form-id'
        ]);

        $client = $this->prophesize(Client::class);
        $client->webhookExists(Argument::that(function($arg) use ($webhook) {
            return $arg instanceof Webhook && $arg->is($webhook);
        }))->shouldBeCalled()->willReturn(true);
        $client->webhookEnabled(Argument::that(function($arg) use ($webhook) {
            return $arg instanceof Webhook && $arg->is($webhook);
        }))->shouldBeCalled()->willReturn(true);

        $job = new DummyWebhookJob($this->getModuleInstance());
        $job->setClient($client->reveal());
        $job->handle();
    }


    /** @test */
    public function it_creates_the_webhook_on_typeform_if_it_does_not_exist(){
        ModuleInstanceSetting::create(['module_instance_id' => $this->getModuleInstance()->id, 'key' => 'collect_responses', 'value' => true]);
        ModuleInstanceSetting::create(['module_instance_id' => $this->getModuleInstance()->id, 'key' => 'use_webhook', 'value' => true]);
        ModuleInstanceSetting::create(['module_instance_id' => $this->getModuleInstance()->id, 'key' => 'form_id', 'value' => 'a-form-id']);

        $webhook = Webhook::factory()->create([
            'module_instance_id' => $this->getModuleInstance()->id(),
            'tag' => Webhook::generatedTag($this->getModuleInstance()),
            'form_id' => 'a-form-id'
        ]);

        $client = $this->prophesize(Client::class);
        $client->webhookExists(Argument::that(function($arg) use ($webhook) {
            return $arg instanceof Webhook && $arg->is($webhook);
        }))->shouldBeCalled()->willReturn(false);
        $client->webhookCreate(Argument::that(function($arg) use ($webhook) {
            return $arg instanceof Webhook && $arg->is($webhook);
        }))->shouldBeCalled();
        $client->webhookEnabled(Argument::that(function($arg) use ($webhook) {
            return $arg instanceof Webhook && $arg->is($webhook);
        }))->shouldBeCalled()->willReturn(true);

        $job = new DummyWebhookJob($this->getModuleInstance());
        $job->setClient($client->reveal());
        $job->handle();
    }

    /** @test */
    public function it_enables_the_webhook_on_typeform_if_it_is_not_enabled(){
        ModuleInstanceSetting::create(['module_instance_id' => $this->getModuleInstance()->id, 'key' => 'collect_responses', 'value' => true]);
        ModuleInstanceSetting::create(['module_instance_id' => $this->getModuleInstance()->id, 'key' => 'use_webhook', 'value' => true]);
        ModuleInstanceSetting::create(['module_instance_id' => $this->getModuleInstance()->id, 'key' => 'form_id', 'value' => 'a-form-id']);

        $webhook = Webhook::factory()->create([
            'module_instance_id' => $this->getModuleInstance()->id(),
            'tag' => Webhook::generatedTag($this->getModuleInstance()),
            'form_id' => 'a-form-id'
        ]);

        $client = $this->prophesize(Client::class);
        $client->webhookExists(Argument::that(function($arg) use ($webhook) {
            return $arg instanceof Webhook && $arg->is($webhook);
        }))->shouldBeCalled()->willReturn(true);
        $client->webhookEnabled(Argument::that(function($arg) use ($webhook) {
            return $arg instanceof Webhook && $arg->is($webhook);
        }))->shouldBeCalled()->willReturn(false);
        $client->webhookEnable(Argument::that(function($arg) use ($webhook) {
            return $arg instanceof Webhook && $arg->is($webhook);
        }))->shouldBeCalled();

        $job = new DummyWebhookJob($this->getModuleInstance());
        $job->setClient($client->reveal());
        $job->handle();
    }

    /** @test */
    public function it_creates_a_webhook_model_if_one_not_found(){
        ModuleInstanceSetting::create(['module_instance_id' => $this->getModuleInstance()->id, 'key' => 'collect_responses', 'value' => true]);
        ModuleInstanceSetting::create(['module_instance_id' => $this->getModuleInstance()->id, 'key' => 'use_webhook', 'value' => true]);
        ModuleInstanceSetting::create(['module_instance_id' => $this->getModuleInstance()->id, 'key' => 'form_id', 'value' => 'a-form-id']);

        $client = $this->prophesize(Client::class);
        $client->webhookExists(Argument::that(function($arg) {
            return $arg instanceof Webhook
                && $arg->module_instance_id == $this->getModuleInstance()->id()
                && $arg->tag === Webhook::generatedTag($this->getModuleInstance())
                && $arg->form_id === 'a-form-id';
        }))->shouldBeCalled()->willReturn(true);
        $client->webhookEnabled(Argument::that(function($arg) {
            return $arg instanceof Webhook
                && $arg->module_instance_id == $this->getModuleInstance()->id()
                && $arg->tag === Webhook::generatedTag($this->getModuleInstance())
                && $arg->form_id === 'a-form-id';
        }))->shouldBeCalled()->willReturn(true);

        $job = new DummyWebhookJob($this->getModuleInstance());
        $job->setClient($client->reveal());
        $job->handle();
    }

    /** @test */
    public function it_disables_an_enabled_webhook_if_the_module_does_not_use_a_webhook(){
        ModuleInstanceSetting::create(['module_instance_id' => $this->getModuleInstance()->id, 'key' => 'collect_responses', 'value' => true]);
        ModuleInstanceSetting::create(['module_instance_id' => $this->getModuleInstance()->id, 'key' => 'use_webhook', 'value' => false]);
        ModuleInstanceSetting::create(['module_instance_id' => $this->getModuleInstance()->id, 'key' => 'form_id', 'value' => 'a-form-id']);

        $webhook = Webhook::factory()->create([
            'module_instance_id' => $this->getModuleInstance()->id(),
            'tag' => Webhook::generatedTag($this->getModuleInstance()),
            'form_id' => 'a-form-id'
        ]);

        $client = $this->prophesize(Client::class);
        $client->webhookExists(Argument::that(function($arg) use ($webhook){
            return $arg instanceof Webhook && $arg->is($webhook);
        }))->shouldBeCalled()->willReturn(true);
        $client->webhookEnabled(Argument::that(function($arg) use ($webhook){
            return $arg instanceof Webhook && $arg->is($webhook);
        }))->shouldBeCalled()->willReturn(true);
        $client->webhookDisable(Argument::that(function($arg) use ($webhook){
            return $arg instanceof Webhook && $arg->is($webhook);
        }))->shouldBeCalled();

        $job = new DummyWebhookJob($this->getModuleInstance());
        $job->setClient($client->reveal());
        $job->handle();
    }

    /** @test */
    public function it_does_nothing_if_a_module_not_using_a_webhook_already_has_a_disabled_webhook(){
        ModuleInstanceSetting::create(['module_instance_id' => $this->getModuleInstance()->id, 'key' => 'collect_responses', 'value' => true]);
        ModuleInstanceSetting::create(['module_instance_id' => $this->getModuleInstance()->id, 'key' => 'use_webhook', 'value' => false]);
        ModuleInstanceSetting::create(['module_instance_id' => $this->getModuleInstance()->id, 'key' => 'form_id', 'value' => 'a-form-id']);

        $webhook = Webhook::factory()->create([
            'module_instance_id' => $this->getModuleInstance()->id(),
            'tag' => Webhook::generatedTag($this->getModuleInstance()),
            'form_id' => 'a-form-id'
        ]);

        $client = $this->prophesize(Client::class);
        $client->webhookExists(Argument::that(function($arg) use ($webhook){
            return $arg instanceof Webhook && $arg->is($webhook);
        }))->shouldBeCalled()->willReturn(true);
        $client->webhookEnabled(Argument::that(function($arg) use ($webhook){
            return $arg instanceof Webhook && $arg->is($webhook);
        }))->shouldBeCalled()->willReturn(false);
        $client->webhookDisable(Argument::that(function($arg) use ($webhook){
            return $arg instanceof Webhook && $arg->is($webhook);
        }))->shouldNotBeCalled();

        $job = new DummyWebhookJob($this->getModuleInstance());
        $job->setClient($client->reveal());
        $job->handle();
    }

}

class DummyWebhookJob extends SyncWebhookStatus
{
    protected $client;

    public function setClient(Client $client)
    {
        $this->client = $client;
    }

    protected function resolveClient()
    {
        return ($this->client??parent::resolveClient());
    }
}
