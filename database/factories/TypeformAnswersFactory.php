<?php

namespace Database\Typeform\Factories;

use BristolSU\Module\Typeform\Models\Answer;
use Illuminate\Database\Eloquent\Factories\Factory;

class TypeformAnswersFactory extends Factory
{

    protected $model = Answer::class;

    public function definition()
    {
        return [
            'field_id' => function() {
                return \BristolSU\Module\Typeform\Models\Field::factory()->create()->id;
            },
            'response_id' => function() {
                return \BristolSU\Module\Typeform\Models\Response::factory()->create()->id;
            },
            'type' => $this->faker->randomElement(['text', 'choice', 'choices', 'email', 'url', 'file_url', 'boolean', 'number', 'date', 'payment']),
            'answer' => null,
            'encoded' => false
        ];
    }
}
