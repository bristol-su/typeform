<?php

$factory->define(\BristolSU\Module\Typeform\Models\Field::class, function(\Faker\Generator $faker) {
    return [
        'id' => $faker->unique()->uuid,
        'form_id' => $faker->unique()->numberBetween(0, 1000000),
        'type' => $faker->randomElement([
            'short_text', 'long_text', 'dropdown', 'multiple_choice', 'picture_choice', 'email', 'website', 'file_upload',
            'legal', 'yes_no', 'rating', 'opinion_scale', 'number', 'date', 'payment'
        ]),
        'title' => $faker->word
    ];
});