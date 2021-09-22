<?php

namespace BristolSU\Module\Tests\Typeform\Commands;

use BristolSU\Module\Tests\Typeform\TestCase;
use BristolSU\Module\Typeform\Commands\CheckResponses;
use BristolSU\Module\Typeform\Jobs\UpdateResponses as CheckResponsesJob;
use BristolSU\Support\ModuleInstance\ModuleInstance;
use BristolSU\Support\ModuleInstance\Settings\ModuleInstanceSetting;
use Illuminate\Support\Facades\Bus;

class CheckResponsesTest extends TestCase
{
    /** @test */
    public function it_dispatches_jobs_for_all_typeform_module_instances(){
        Bus::fake(CheckResponsesJob::class);

        $moduleInstances = ModuleInstance::factory()->count(8)->create([
            'alias' => 'typeform'
        ]);
        foreach($moduleInstances as $moduleInstance) {
            ModuleInstanceSetting::create(['module_instance_id' => $moduleInstance->id, 'key' => 'collect_responses', 'value' => true]);
            ModuleInstanceSetting::create(['module_instance_id' => $moduleInstance->id, 'key' => 'use_webhook', 'value' => false]);
            ModuleInstanceSetting::create(['module_instance_id' => $moduleInstance->id, 'key' => 'form_id', 'value' => 'dd']);
        }

        $otherModule1 = ModuleInstance::factory()->create([
            'alias' => 'typeform'
        ]);
        ModuleInstanceSetting::create(['module_instance_id' => $otherModule1->id, 'key' => 'collect_responses', 'value' => true]);
        ModuleInstanceSetting::create(['module_instance_id' => $otherModule1->id, 'key' => 'use_webhook', 'value' => false]);
        ModuleInstanceSetting::create(['module_instance_id' => $otherModule1->id, 'key' => 'form_id', 'value' => null]);

        $otherModule2 = ModuleInstance::factory()->create([
            'alias' => 'typeform'
        ]);
        ModuleInstanceSetting::create(['module_instance_id' => $otherModule2->id, 'key' => 'collect_responses', 'value' => true]);
        ModuleInstanceSetting::create(['module_instance_id' => $otherModule2->id, 'key' => 'use_webhook', 'value' => true]);
        ModuleInstanceSetting::create(['module_instance_id' => $otherModule2->id, 'key' => 'form_id', 'value' => 'id_here']);

        $otherModule3 = ModuleInstance::factory()->create([
            'alias' => 'typeform'
        ]);
        ModuleInstanceSetting::create(['module_instance_id' => $otherModule3->id, 'key' => 'collect_responses', 'value' => false]);
        ModuleInstanceSetting::create(['module_instance_id' => $otherModule3->id, 'key' => 'use_webhook', 'value' => false]);
        ModuleInstanceSetting::create(['module_instance_id' => $otherModule2->id, 'key' => 'form_id', 'value' => 'id_here']);

        $otherModule4 = ModuleInstance::factory()->create(['alias' => 'other']);
        ModuleInstanceSetting::create(['module_instance_id' => $otherModule4->id, 'key' => 'collect_responses', 'value' => true]);
        ModuleInstanceSetting::create(['module_instance_id' => $otherModule4->id, 'key' => 'use_webhook', 'value' => false]);
        ModuleInstanceSetting::create(['module_instance_id' => $otherModule4->id, 'key' => 'form_id', 'value' => 'dddd']);


        $this->artisan(CheckResponses::class);

        foreach($moduleInstances as $moduleInstance) {
            Bus::assertDispatched(CheckResponsesJob::class, function($job) use ($moduleInstance) {
                return $job instanceof CheckResponsesJob && $job->moduleInstance->is($moduleInstance);
            });
        }

        foreach([$otherModule1, $otherModule2, $otherModule3, $otherModule4] as $otherModuleInstance) {
            Bus::assertNotDispatched(CheckResponsesJob::class, function($job) use ($otherModuleInstance) {
                return $job instanceof CheckResponsesJob && $job->moduleInstance->is($otherModuleInstance);
            });
        }
    }

    /** @test */
    public function it_only_dispatches_the_job_for_the_given_module_instance_if_given(){
        Bus::fake(CheckResponsesJob::class);

        $moduleInstances = ModuleInstance::factory()->count(8)->create([
            'alias' => 'typeform'
        ]);
        foreach($moduleInstances as $moduleInstance) {
            ModuleInstanceSetting::create(['module_instance_id' => $moduleInstance->id, 'key' => 'collect_responses', 'value' => true]);
            ModuleInstanceSetting::create(['module_instance_id' => $moduleInstance->id, 'key' => 'use_webhook', 'value' => false]);
            ModuleInstanceSetting::create(['module_instance_id' => $moduleInstance->id, 'key' => 'form_id', 'value' => 'dd']);
        }
        $moduleInstance1 = ModuleInstance::factory()->create([
            'alias' => 'typeform'
        ]);
        ModuleInstanceSetting::create(['module_instance_id' => $moduleInstance1->id, 'key' => 'collect_responses', 'value' => true]);
        ModuleInstanceSetting::create(['module_instance_id' => $moduleInstance1->id, 'key' => 'use_webhook', 'value' => false]);
        ModuleInstanceSetting::create(['module_instance_id' => $moduleInstance1->id, 'key' => 'form_id', 'value' => 'dd']);

        $this->artisan(CheckResponses::class, ['moduleinstance' => $moduleInstance1->id()]);

        Bus::assertDispatched(CheckResponsesJob::class, function($job) use ($moduleInstance1) {
            return $job instanceof CheckResponsesJob && $job->moduleInstance->is($moduleInstance1);
        });

        foreach($moduleInstances as $moduleInstance) {
            Bus::assertNotDispatched(CheckResponsesJob::class, function($job) use ($moduleInstance) {
                return $job instanceof CheckResponsesJob && $job->moduleInstance->is($moduleInstance);
            });
        }
    }
}
