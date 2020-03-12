<?php

$factory->define(\BristolSU\Module\Typeform\Models\Webhook::class, function(\Faker\Generator $faker) {
    return [
        'module_instance_id' => function() {
            return factory(\BristolSU\Support\ModuleInstance\ModuleInstance::class)->create()->id;
        },
        'tag' => $faker->word,
        'form_id' => $faker->bothify('##???#')
    ];
});