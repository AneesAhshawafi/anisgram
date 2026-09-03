<?php

namespace Database\Factories;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Post>
 */
class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $images = ['pic1.png', 'pic2.png', 'pic3.png', 'pic4.png'];

        return [
            'description' => fake()->sentence(),
            'slug' => fake()->regexify('[A-Za-z0-9]{10}'),
            'user_id' => User::factory(),
            'image' => 'posts/'.fake()->randomElement($images),

        ];
    }
}
