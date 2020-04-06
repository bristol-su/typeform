<?php

$factory->define(\BristolSU\Module\Typeform\Models\Comment::class, function(\Faker\Generator $faker) {
    return [
        'response_id' => function() {
            return factory(\BristolSU\Module\Typeform\Models\Response::class)->create()->id;
        },
        'comment' => $faker->sentence,
        'posted_by' => function() {
            return factory(\BristolSU\ControlDB\Models\User::class)->create()->id();
        }
    ];
});