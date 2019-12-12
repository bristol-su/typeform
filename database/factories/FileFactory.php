<?php

use BristolSU\Module\Typeform\Models\File;
use Faker\Generator as Faker;

$factory->define(File::class, function (Faker $faker) {
    return [
        'title' => $faker->sentence,
        'description' => $faker->sentence,
        'filename' => $faker->word,
        'mime' => $faker->mimeType,
        'path' => \Illuminate\Support\Str::random(40),
        'size' => $faker->numberBetween(500, 99999999),
        'uploaded_by' => null,
        'module_instance_id' => function () {
            return factory(\BristolSU\Support\ModuleInstance\ModuleInstance::class)->create()->id;
        },
        'resource_type' => 'user',
        'resource_id' => null
    ];
});