<?php

namespace BristolSU\Module\Tests\Typeform\Models;

use BristolSU\Module\Tests\Typeform\TestCase;
use BristolSU\Module\Typeform\Models\Answer;
use BristolSU\Module\Typeform\Models\Field;

class FieldTest extends TestCase
{

    /** @test */
    public function it_can_be_created(){
        $field = Field::factory()->create([
            'id' => 'afhwi243f47h4',
            'form_id' => 'abc235',
            'type' => 'yes_no',
            'title' => 'Some Typeform Question can be referenced here. This is the same...'
        ]);

        $this->assertDatabaseHas('typeform_fields', [
            'id' => 'afhwi243f47h4',
            'form_id' => 'abc235',
            'type' => 'yes_no',
            'title' => 'Some Typeform Question can be referenced here. This is the same...'
        ]);
    }

    /** @test */
    public function it_has_many_answers(){
        $field = Field::factory()->create();
        $answers = Answer::factory()->count(5)->create(['field_id' => $field->id]);
        $nonAnswers = Answer::factory()->count(2)->create();

        $result = $field->answers;
        $this->assertCount(5, $result);
        $this->assertContainsOnlyInstancesOf(Answer::class, $result);
        foreach($answers as $answer) {
            $this->assertModelEquals($answer, $result->shift());
        }
    }

}
