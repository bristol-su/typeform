<?php

namespace BristolSU\Module\Tests\Typeform\Http\Controllers\Participant;

use BristolSU\Module\Tests\Typeform\TestCase;
use BristolSU\Module\Typeform\Models\Response;

class ParticipantPageControllerTest extends TestCase
{
    
    /** @test */
    public function index_returns_a_403_if_the_permission_is_not_owned(){
        $this->revokePermissionTo('typeform.view-form');
        
        $response = $this->get($this->userUrl('/'));
        $response->assertStatus(403);
    }

    /** @test */
    public function index_returns_a_200_if_the_permission_is_owned(){
        $this->givePermissionTo('typeform.view-form');

        $response = $this->get($this->userUrl('/'));
        $response->assertStatus(200);
    }
    
    /** @test */
    public function index_returns_the_correct_view(){
        $this->bypassAuthorization();
        
        $response = $this->get($this->userUrl('/'));
        $response->assertViewIs('typeform::participant');
    }

    /** @test */
    public function index_passes_the_responses_to_the_view(){
        $this->bypassAuthorization();

        $responses = factory(Response::class, 5)->create(['module_instance_id' => $this->getModuleInstance()->id(), 'activity_instance_id' => $this->getActivityInstance()->id]);
        factory(Response::class, 4)->create(['module_instance_id' => $this->getModuleInstance()->id()]);
        factory(Response::class, 4)->create();
        
        $response = $this->get($this->userUrl('/'));
        $response->assertViewHas('responses');
        $viewData = $response->viewData('responses');
        $this->assertCount(5, $viewData);
        foreach($responses as $formResponse) {
            $this->assertModelEquals($formResponse, $viewData->shift());
        }
    }

}