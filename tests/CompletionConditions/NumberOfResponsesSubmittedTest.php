<?php

namespace BristolSU\Module\Tests\Typeform\CompletionConditions;

use BristolSU\Module\Tests\Typeform\TestCase;
use BristolSU\Module\Typeform\CompletionConditions\NumberOfResponsesSubmitted;
use BristolSU\Module\Typeform\Models\Response;
use BristolSU\Support\ActivityInstance\ActivityInstance;
use BristolSU\Support\ModuleInstance\ModuleInstance;
use FormSchema\Schema\Form;

class NumberOfResponsesSubmittedTest extends TestCase
{

    /** @test */
    public function name_returns_a_string(){
        $condition = new NumberOfResponsesSubmitted('typeform');
        $this->assertIsString($condition->name());
    }

    /** @test */
    public function description_returns_a_string(){
        $condition = new NumberOfResponsesSubmitted('typeform');
        $this->assertIsString($condition->description());
    }

    /** @test */
    public function alias_returns_a_string(){
        $condition = new NumberOfResponsesSubmitted('typeform');
        $this->assertIsString($condition->name());
        $this->assertEquals('number_of_responses_submitted', $condition->alias());
    }
    
    /** @test */
    public function options_returns_a_form_schema(){
        $condition = new NumberOfResponsesSubmitted('typeform');
        $this->assertInstanceOf(Form::class, $condition->options());
    }
    
    /** @test */
    public function isComplete_returns_true_if_the_num_of_responses_is_equal_to_the_setting(){
        $activityInstance = factory(ActivityInstance::class)->create();
        $moduleInstance = factory(ModuleInstance::class)->create();
        
        factory(Response::class, 2)->create(['activity_instance_id' => $activityInstance->id, 'module_instance_id' => $moduleInstance->id()]);
        
        $condition = new NumberOfResponsesSubmitted('typeform');
        $this->assertTrue(
            $condition->isComplete(['number_of_responses' => 2], $activityInstance, $moduleInstance)
        );
    }

    /** @test */
    public function isComplete_returns_true_if_the_num_of_responses_is_greater_than_the_setting(){
        $activityInstance = factory(ActivityInstance::class)->create();
        $moduleInstance = factory(ModuleInstance::class)->create();

        factory(Response::class, 5)->create(['activity_instance_id' => $activityInstance->id, 'module_instance_id' => $moduleInstance->id()]);

        $condition = new NumberOfResponsesSubmitted('typeform');
        $this->assertTrue(
            $condition->isComplete(['number_of_responses' => 2], $activityInstance, $moduleInstance)
        );
    }

    /** @test */
    public function isComplete_returns_false_if_the_num_of_responses_is_less_than_the_setting(){
        $activityInstance = factory(ActivityInstance::class)->create();
        $moduleInstance = factory(ModuleInstance::class)->create();

        factory(Response::class, 1)->create(['activity_instance_id' => $activityInstance->id, 'module_instance_id' => $moduleInstance->id()]);

        $condition = new NumberOfResponsesSubmitted('typeform');
        $this->assertFalse(
            $condition->isComplete(['number_of_responses' => 2], $activityInstance, $moduleInstance)
        );
    }
    
    /** @test */
    public function percentage_returns_100_if_the_num_of_responses_is_equal_to_the_setting(){
        $activityInstance = factory(ActivityInstance::class)->create();
        $moduleInstance = factory(ModuleInstance::class)->create();

        factory(Response::class, 2)->create(['activity_instance_id' => $activityInstance->id, 'module_instance_id' => $moduleInstance->id()]);

        $condition = new NumberOfResponsesSubmitted('typeform');
        $this->assertEquals(100, 
            $condition->percentage(['number_of_responses' => 2], $activityInstance, $moduleInstance)
        );
    }

    /** @test */
    public function percentage_returns_100_if_the_num_of_responses_is_greater_than_the_setting(){
        $activityInstance = factory(ActivityInstance::class)->create();
        $moduleInstance = factory(ModuleInstance::class)->create();

        factory(Response::class, 6)->create(['activity_instance_id' => $activityInstance->id, 'module_instance_id' => $moduleInstance->id()]);

        $condition = new NumberOfResponsesSubmitted('typeform');
        $this->assertEquals(100,
            $condition->percentage(['number_of_responses' => 2], $activityInstance, $moduleInstance)
        );
    }
    
    /** @test */
    public function percentage_returns_50_if_the_num_of_responses_is_half_the_setting(){
        $activityInstance = factory(ActivityInstance::class)->create();
        $moduleInstance = factory(ModuleInstance::class)->create();

        factory(Response::class, 1)->create(['activity_instance_id' => $activityInstance->id, 'module_instance_id' => $moduleInstance->id()]);

        $condition = new NumberOfResponsesSubmitted('typeform');
        $this->assertEquals(50,
            $condition->percentage(['number_of_responses' => 2], $activityInstance, $moduleInstance)
        );
    }

    /** @test */
    public function percentage_returns_25_if_the_num_of_responses_is_a_quarter_of_the_setting(){
        $activityInstance = factory(ActivityInstance::class)->create();
        $moduleInstance = factory(ModuleInstance::class)->create();

        factory(Response::class, 2)->create(['activity_instance_id' => $activityInstance->id, 'module_instance_id' => $moduleInstance->id()]);

        $condition = new NumberOfResponsesSubmitted('typeform');
        $this->assertEquals(25,
            $condition->percentage(['number_of_responses' => 8], $activityInstance, $moduleInstance)
        );
    }

    /** @test */
    public function percentage_returns_0_if_no_responses_submitted(){
        $activityInstance = factory(ActivityInstance::class)->create();
        $moduleInstance = factory(ModuleInstance::class)->create();

        $condition = new NumberOfResponsesSubmitted('typeform');
        $this->assertEquals(0,
            $condition->percentage(['number_of_responses' => 2], $activityInstance, $moduleInstance)
        );
    }
    
}