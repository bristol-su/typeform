<?php

namespace Database\Typeform\Factories;

use BristolSU\Module\Typeform\Models\Webhook;
use Illuminate\Database\Eloquent\Factories\Factory;

class TypeformWebhookFactory extends Factory
{

    protected $model = Webhook::class;

    public function definition()
    {
        return [
            'module_instance_id' => fn() => \BristolSU\Support\ModuleInstance\ModuleInstance::factory()->create()->id,
            'tag' => $this->faker->word,
            'form_id' => $this->faker->bothify('##???#')
        ];
    }
}
