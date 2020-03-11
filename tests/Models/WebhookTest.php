<?php

namespace BristolSU\Module\Tests\Typeform\Models;

use BristolSU\Module\Tests\Typeform\TestCase;
use BristolSU\Module\Typeform\Models\Webhook;
use BristolSU\Support\Activity\Activity;
use BristolSU\Support\ModuleInstance\ModuleInstance;
use BristolSU\Support\ModuleInstance\Settings\ModuleInstanceSetting;

class WebhookTest extends TestCase
{

    /** @test */
    public function it_can_be_created(){
        $webhook = factory(Webhook::class)->create([
            'module_instance_id' => $this->getModuleInstance()->id(),
            'tag' => 'SomeTag',
            'form_id' => '272Hd2'
        ]);
        
        $this->assertDatabaseHas('typeform_webhooks', [
            'id' => $webhook->id,
            'module_instance_id' => $this->getModuleInstance()->id(),
            'tag' => 'SomeTag',
            'form_id' => '272Hd2'
        ]);
    }
    
    /** @test */
    public function it_generates_a_tag_from_a_module_instance(){
        $activity = factory(Activity::class)->create(['slug' => 'ActSlug']);
        $moduleInstance = factory(ModuleInstance::class)->create([
            'alias' => 'typeform',
            'activity_id' => $activity->id,
            'slug' => 'ModInstSlug'
        ]);
        
        ModuleInstanceSetting::create([
            'module_instance_id' => $moduleInstance->id,
            'key' => 'form_id',
            'value' => 'abc123'
        ]);
        
        $this->assertEquals('ActSlug-ModInstSlug-abc123', Webhook::generatedTag($moduleInstance));
    }
    
    /** @test */
    public function it_retrieves_the_module_instance(){
        $moduleInstance = factory(ModuleInstance::class)->create();
        $webhook = factory(Webhook::class)->create([
            'module_instance_id' => $moduleInstance->id
        ]);
        $this->assertInstanceOf(ModuleInstance::class, $webhook->moduleInstance());
        $this->assertModelEquals($moduleInstance, $webhook->moduleInstance());
    }
    
    /** @test */
    public function it_generates_the_callback_url_to_send_payloads_to(){
        $activity = factory(Activity::class)->create(['slug' => 'ActSlug']);
        $moduleInstance = factory(ModuleInstance::class)->create([
            'alias' => 'typeform',
            'activity_id' => $activity->id,
            'slug' => 'ModInstSlug'
        ]);
        $webhook = factory(Webhook::class)->create([
            'module_instance_id' => $moduleInstance->id
        ]);
        
        $this->assertEquals('http://localhost/api/a/ActSlug/ModInstSlug/typeform/webhook/responses', $webhook->url());
    }
    
    /** @test */
    public function fromModuleInstance_returns_the_correct_query_parameters(){
        $activity = factory(Activity::class)->create(['slug' => 'A']);
        $moduleInstance1 = factory(ModuleInstance::class)->create(['activity_id' => $activity->id, 'slug' => 'M1']);
        $moduleInstance2 = factory(ModuleInstance::class)->create(['activity_id' => $activity->id, 'slug' => 'M2']);
        $moduleInstance3 = factory(ModuleInstance::class)->create(['activity_id' => $activity->id, 'slug' => 'M3']);

        $webhook1 = factory(Webhook::class)->create(['module_instance_id' => $moduleInstance1->id(), 'form_id' => 'abc123', 'tag' => 'A-M1-abc123']);
        $webhook2 = factory(Webhook::class)->create(['module_instance_id' => $moduleInstance2->id(), 'form_id' => 'abc123', 'tag' => 'A-M2-abc123']);
        $webhook3 = factory(Webhook::class)->create(['module_instance_id' => $moduleInstance3->id(), 'form_id' => '123abc', 'tag' => 'A-M3-123abc']);
        
        ModuleInstanceSetting::create(['module_instance_id' => $moduleInstance1->id, 'key' => 'form_id', 'value' => 'abc123']);
        ModuleInstanceSetting::create(['module_instance_id' => $moduleInstance2->id, 'key' => 'form_id', 'value' => 'abc123']);
        ModuleInstanceSetting::create(['module_instance_id' => $moduleInstance3->id, 'key' => 'form_id', 'value' => '123abc']);
        
        $result = Webhook::fromModuleInstance($moduleInstance1)->get();
        $this->assertCount(1, $result);
        $this->assertInstanceOf(Webhook::class, $result[0]);
        $this->assertModelEquals($webhook1, $result[0]);

        $result = Webhook::fromModuleInstance($moduleInstance2)->get();
        $this->assertCount(1, $result);
        $this->assertInstanceOf(Webhook::class, $result[0]);
        $this->assertModelEquals($webhook2, $result[0]);

        $result = Webhook::fromModuleInstance($moduleInstance3)->get();
        $this->assertCount(1, $result);
        $this->assertInstanceOf(Webhook::class, $result[0]);
        $this->assertModelEquals($webhook3, $result[0]);
    }
    
}