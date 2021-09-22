<?php

namespace Database\Typeform\Factories;

use BristolSU\Module\Typeform\Models\Field;
use Illuminate\Database\Eloquent\Factories\Factory;

class TypeformFieldsFactory extends Factory
{

    protected $model = Field::class;

    public function definition()
    {
        return [
            'id' => $this->faker->unique()->uuid,
            'form_id' => $this->faker->unique()->numberBetween(0, 1000000),
            'type' => $this->faker->randomElement([
                'short_text', 'long_text', 'dropdown', 'multiple_choice', 'picture_choice', 'email', 'website', 'file_upload',
                'legal', 'yes_no', 'rating', 'opinion_scale', 'number', 'date', 'payment'
            ]),
            'title' => $this->faker->word
        ];
    }
}
