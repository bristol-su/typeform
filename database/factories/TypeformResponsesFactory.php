<?php

$factory->define(\BristolSU\Module\Typeform\Models\Response::class, function(\Faker\Generator $faker) {
    return [
        'id' => $faker->unique()->uuid,
        'form_id' => $faker->unique()->numberBetween(0, 1000000),
        'submitted_by' => function() {
            return factory(\BristolSU\ControlDB\Models\User::class)->create()->id();
        },
        'module_instance_id' => function() {
            return factory(\BristolSU\Support\ModuleInstance\ModuleInstance::class)->create()->id();
        },
        'activity_instance_id' => function() {
            return factory(\BristolSU\Support\ActivityInstance\ActivityInstance::class)->create()->id;
        },
        'submitted_at' => $faker->dateTime,
        'approved' => $faker->boolean
    ];
});