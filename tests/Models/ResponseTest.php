<?php

namespace BristolSU\Module\Tests\Typeform\Models;

use BristolSU\ControlDB\Models\User;
use BristolSU\Module\Tests\Typeform\TestCase;
use BristolSU\Module\Typeform\Models\Answer;
use BristolSU\Module\Typeform\Models\Response;
use BristolSU\Support\ActivityInstance\ActivityInstance;
use BristolSU\Support\ModuleInstance\ModuleInstance;
use Carbon\Carbon;

class ResponseTest extends TestCase
{

    /** @test */
    public function it_can_be_created(){
        $user = factory(User::class)->create();
        $moduleInstance = factory(ModuleInstance::class)->create();
        $activityInstance = factory(ActivityInstance::class)->create();
        $submittedAt = Carbon::now()->subDay();
        
        $response = factory(Response::class)->create([
            'id' => 'jt5sdfkh38ns9h',
            'form_id' => 'abc1234',
            'submitted_by' => $user->id,
            'module_instance_id' => $moduleInstance->id(),
            'activity_instance_id' => $activityInstance->id,
            'submitted_at' => $submittedAt
        ]);
        
        
        $this->assertDatabaseHas('typeform_responses', [
            'id' => $response->id,
            'form_id' => 'abc1234',
            'submitted_by' => $user->id,
            'module_instance_id' => $moduleInstance->id(),
            'activity_instance_id' => $activityInstance->id,
            'submitted_at' => $submittedAt->format('Y-m-d H:i:s')
        ]);
    }
    
    /** @test */
    public function it_has_many_answers(){
        $response = factory(Response::class)->create();
        $answers = factory(Answer::class, 5)->create(['response_id' => $response->id]);
        $nonAnswers = factory(Answer::class, 2)->create();
        
        $result = $response->answers;
        $this->assertCount(5, $result);
        $this->assertContainsOnlyInstancesOf(Answer::class, $result);
        foreach($answers as $answer) {
            $this->assertModelEquals($answer, $result->shift());
        }
    }
    
}