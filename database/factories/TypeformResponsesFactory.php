<?php

namespace Database\Typeform\Factories;

use BristolSU\Module\Typeform\Models\Response;
use Illuminate\Database\Eloquent\Factories\Factory;

class TypeformResponsesFactory extends Factory
{

    protected $model = Response::class;

    public function definition()
    {
        return [
            'id' => $this->faker->unique()->uuid,
            'form_id' => $this->faker->unique()->numberBetween(0, 1000000),
            'submitted_by' => fn() => \BristolSU\ControlDB\Models\User::factory()->create()->id(),
            'module_instance_id' => fn() => \BristolSU\Support\ModuleInstance\ModuleInstance::factory()->create()->id(),
            'activity_instance_id' => fn() => \BristolSU\Support\ActivityInstance\ActivityInstance::factory()->create()->id,
            'submitted_at' => $this->faker->dateTime,
            'approved' => $this->faker->boolean
        ];
    }
}
