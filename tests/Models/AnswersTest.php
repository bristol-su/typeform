<?php

namespace BristolSU\Module\Tests\Typeform\Models;

use BristolSU\Module\Tests\Typeform\TestCase;
use BristolSU\Module\Typeform\Models\Answer;
use BristolSU\Module\Typeform\Models\Field;
use BristolSU\Module\Typeform\Models\Response;

class AnswersTest extends TestCase
{

    /** @test */
    public function it_has_a_field(){
        $field = Field::factory()->create();
        $answer = Answer::factory()->create(['field_id' => $field->id]);

        $this->assertInstanceOf(Field::class, $answer->field);
        $this->assertModelEquals($field, $answer->field);
    }

    /** @test */
    public function it_has_a_response(){
        $response = Response::factory()->create();
        $answer = Answer::factory()->create(['response_id' => $response->id]);

        $this->assertInstanceOf(Response::class, $answer->response);
        $this->assertModelEquals($response, $answer->response);
    }

    /** @test */
    public function it_sets_and_gets_a_non_array_value_with_a_mutator_and_accessor(){
        $answer = Answer::factory()->create([
            'answer' => 'SomeValue'
        ]);

        $this->assertDatabaseHas('typeform_answers', [
            'id' => $answer->id,
            'answer' => 'SomeValue',
            'encoded' => false
        ]);
        $this->assertEquals('SomeValue', $answer->answer);
    }

    /** @test */
    public function it_encodes_and_decodes_an_array_value_with_a_mutator_and_accessor(){
        $answer = Answer::factory()->make();
        $answer->answer = ['SomeKey' => 'SomeValue'];
        $answer->save();

        $this->assertDatabaseHas('typeform_answers', [
            'id' => $answer->id,
            'answer' => '{"SomeKey":"SomeValue"}',
            'encoded' => true
        ]);

        $this->assertIsArray($answer->answer);
        $this->assertEquals(['SomeKey' => 'SomeValue'], $answer->answer);
    }

}
