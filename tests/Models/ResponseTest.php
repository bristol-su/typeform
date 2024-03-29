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
        $user = User::factory()->create();
        $moduleInstance = ModuleInstance::factory()->create();
        $activityInstance = ActivityInstance::factory()->create();
        $submittedAt = Carbon::now()->subDay();

        $response = Response::factory()->create([
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
        $response = Response::factory()->create();
        $answers = Answer::factory()->count(5)->create(['response_id' => $response->id]);
        $nonAnswers = Answer::factory()->count(2)->create();

        $result = $response->answers;
        $this->assertCount(5, $result);
        $this->assertContainsOnlyInstancesOf(Answer::class, $result);
        foreach($answers as $answer) {
            $this->assertModelEquals($answer, $result->shift());
        }
    }

}
