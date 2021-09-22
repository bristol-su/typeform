<?php

namespace Database\Typeform\Factories;

use BristolSU\Module\Typeform\Models\Comment;
use Illuminate\Database\Eloquent\Factories\Factory;

class TypeformCommentFactory extends Factory
{

    protected $model = Comment::class;

    public function definition()
    {
        return [
            'response_id' => function() {
                return \BristolSU\Module\Typeform\Models\Response::factory()->create()->id;
            },
            'comment' => $this->faker->sentence,
            'posted_by' => function() {
                return \BristolSU\ControlDB\Models\User::factory()->create()->id();
            }
        ];
    }
}
