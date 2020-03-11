<?php

$factory->define(\BristolSU\Module\Typeform\Models\Answer::class, function(\Faker\Generator $faker) {
    return [
        'field_id' => function() {
            return factory(\BristolSU\Module\Typeform\Models\Field::class)->create()->id;
        },
        'response_id' => function() {
            return factory(\BristolSU\Module\Typeform\Models\Response::class)->create()->id;
        },
        'type' => $faker->randomElement(['text', 'choice', 'choices', 'email', 'url', 'file_url', 'boolean', 'number', 'date', 'payment']),
        'answer' => null,
        'encoded' => false
    ];
});