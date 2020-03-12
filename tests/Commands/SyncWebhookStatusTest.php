<?php

namespace BristolSU\Module\Tests\Typeform\Commands;

use BristolSU\Module\Tests\Typeform\TestCase;
use BristolSU\Module\Typeform\Commands\SyncWebhookStatus;
use BristolSU\Module\Typeform\Jobs\SyncWebhookStatus as SyncWebhookStatusJob;
use BristolSU\Support\ModuleInstance\ModuleInstance;
use Illuminate\Support\Facades\Bus;

class SyncWebhookStatusTest extends TestCase
{
    /** @test */
    public function it_dispatches_jobs_for_all_typeform_module_instances(){
        Bus::fake(SyncWebhookStatusJob::class);
        $moduleInstances = factory(ModuleInstance::class, 8)->create([
            'alias' => 'typeform'
        ]);
        $otherModules = factory(ModuleInstance::class, 2)->create(['alias' => 'other']);
        $this->artisan(SyncWebhookStatus::class);
        
        foreach($moduleInstances as $moduleInstance) {
            Bus::assertDispatched(SyncWebhookStatusJob::class, function($job) use ($moduleInstance) {
                return $job instanceof SyncWebhookStatusJob && $job->moduleInstance->is($moduleInstance);
            });
        }

        foreach($otherModules as $otherModuleInstance) {
            Bus::assertNotDispatched(SyncWebhookStatusJob::class, function($job) use ($otherModuleInstance) {
                return $job instanceof SyncWebhookStatusJob && $job->moduleInstance->is($otherModuleInstance);
            });
        }
    }
    
    /** @test */
    public function it_only_dispatches_the_job_for_the_given_module_instance_if_given(){
        Bus::fake(SyncWebhookStatusJob::class);
        $module = factory(ModuleInstance::class)->create([
            'alias' => 'typeform'
        ]);
        
        $moduleInstances = factory(ModuleInstance::class, 8)->create([
            'alias' => 'typeform'
        ]);
        $otherModules = factory(ModuleInstance::class, 2)->create(['alias' => 'other']);
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