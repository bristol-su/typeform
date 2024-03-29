<?php

namespace BristolSU\Module\Tests\Typeform\Commands;

use BristolSU\Module\Tests\Typeform\TestCase;
use BristolSU\Module\Typeform\Commands\SyncWebhookStatus;
use BristolSU\Module\Typeform\Jobs\SyncWebhookStatus as SyncWebhookStatusJob;
use BristolSU\Support\ModuleInstance\ModuleInstance;
use BristolSU\Support\ModuleInstance\Settings\ModuleInstanceSetting;
use Carbon\Carbon;
use Illuminate\Support\Facades\Bus;

class SyncWebhookStatusTest extends TestCase
{
    /** @test */
    public function it_dispatches_jobs_for_all_typeform_module_instances(){
        Bus::fake(SyncWebhookStatusJob::class);
        $moduleInstances = ModuleInstance::factory()->count(8)->create([
            'alias' => 'typeform'
        ])->each(fn(ModuleInstance $moduleInstance) =>  ModuleInstanceSetting::create(['module_instance_id' => $moduleInstance->id, 'key' => 'collect_responses', 'value' => true, 'updated_at' => Carbon::now()]));

        $otherModuleInstances1 = ModuleInstance::factory()->count(8)->create([
            'alias' => 'typeform'
        ]);
        foreach($otherModuleInstances1 as $module) {
            $setting = ModuleInstanceSetting::create(['module_instance_id' => $module->id, 'key' => 'collect_responses', 'value' => true]);
            $setting->updated_at = Carbon::now()->subWeek();
            $setting->save();
        }

        $otherModules = ModuleInstance::factory()->count(2)->create(['alias' => 'other']);
        $this->artisan(SyncWebhookStatus::class);

        foreach($moduleInstances as $moduleInstance) {
            Bus::assertDispatched(SyncWebhookStatusJob::class, function($job) use ($moduleInstance) {
                return $job instanceof SyncWebhookStatusJob && $job->moduleInstance->is($moduleInstance);
            });
        }

        foreach(array_merge($otherModuleInstances1->all(), $otherModules->all()) as $otherModuleInstance) {
            Bus::assertNotDispatched(SyncWebhookStatusJob::class, function($job) use ($otherModuleInstance) {
                return $job instanceof SyncWebhookStatusJob && $job->moduleInstance->is($otherModuleInstance);
            });
        }
    }

    /** @test */
    public function it_only_dispatches_the_job_for_the_given_module_instance_if_given(){
        Bus::fake(SyncWebhookStatusJob::class);
        $module = ModuleInstance::factory()->create([
            'alias' => 'typeform'
        ]);

        $moduleInstances = ModuleInstance::factory()->count(8)->create([
            'alias' => 'typeform'
        ]);
        $otherModules = ModuleInstance::factory()->count(2)->create(['alias' => 'other']);

        $this->artisan('typeform:webhook ' . $module->id);



        Bus::assertDispatched(SyncWebhookStatusJob::class, function($job) use ($module) {
            return $job instanceof SyncWebhookStatusJob && $job->moduleInstance->is($module);
        });

        foreach($moduleInstances as $moduleInstance) {
            Bus::assertNotDispatched(SyncWebhookStatusJob::class, function($job) use ($moduleInstance) {
                return $job instanceof SyncWebhookStatusJob && $job->moduleInstance->is($moduleInstance);
            });
        }

        foreach($otherModules as $otherModuleInstance) {
            Bus::assertNotDispatched(SyncWebhookStatusJob::class, function($job) use ($otherModuleInstance) {
                return $job instanceof SyncWebhookStatusJob && $job->moduleInstance->is($otherModuleInstance);
            });
        }
    }
}
