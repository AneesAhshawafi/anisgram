<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Post;

use App\Models\Comment;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()
            ->count(10)
            ->has(
                Post::factory()
                    ->count(3)
                    ->has(
                        Comment::factory()
                            ->count(3)
                            ->state(function (array $attributes, Post $post) {
                                return ['user_id' => $post->user_id]; // Author of comment = Author of post
                            })
                    )
            )
            ->create();

        // $users = User::factory()->count(10)->create();

        // Post::factory()
        //     ->count(3)
        //     ->has(Comment::factory()->count(3))
        //     ->recycle($users) // Forces Comments & Posts to use the already created Users
        //     ->create();
        // User::factory()
        //     ->count(10)
        //     ->has(Post::factory()->count(3)->has(Comment::factory()->count(3)))
        //     ->create();
        // User::factory(10)->create();
        // Post::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
    }
}
