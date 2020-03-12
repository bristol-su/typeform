<?php

namespace BristolSU\Module\Tests\Typeform\Http\Controllers\AdminApi;

use BristolSU\Module\Tests\Typeform\TestCase;
use BristolSU\Module\Typeform\Jobs\UpdateResponses;
use Illuminate\Support\Facades\Bus;

class ResponseRefreshControllerTest extends TestCase
{

    /** @test */
    public function it_returns_a_403_if_permission_not_owned(){
        $this->revokePermissionTo('typeform.admin.refresh-form-responses');
        
        $response = $this->post($this->adminApiUrl('/response/refresh'));
        $response->assertStatus(403);
    }
    
    /** @test */
    public function it_returns_a_200_if_permission_owned(){
        $this->givePermissionTo('typeform.admin.refresh-form-responses');

        $response = $this->post($this->adminApiUrl('/response/refresh'));
        $response->assertStatus(200);
    }
    
    /** @test */
    public function it_dispatches_a_job_with_the_correct_module_instance(){
        $this->bypassAuthorization();
        Bus::fake(UpdateResponses::class);

        $response = $this->post($this->adminApiUrl('/response/refresh'));
        $response->assertStatus(200);
        
        Bus::assertDispatched(UpdateResponses::class, function($job) {
            return $job instanceof UpdateResponses && $job->moduleInstance->is($this->getModuleInstance());
        });
    }
    
}