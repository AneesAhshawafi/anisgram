<?php

namespace Database\Factories;

use App\Models\Comment;
use Illuminate\Database\Eloquent\Factories\Factory;

use App\Models\User;
use App\Models\Post;

/**
 * @extends Factory<Comment>
 */
class CommentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            "body" => fake()->sentence(),
            "user_id" => User::factory(),
            "post_id" => Post::factory(),

        ];
    }
}
