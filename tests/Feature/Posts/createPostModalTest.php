<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

uses(RefreshDatabase::class);

/*
|--------------------------------------------------------------------------
| Livewire: posts.create-post-modal Component Tests
|--------------------------------------------------------------------------
*/

it('renders create-post-modal component successfully', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    Livewire::test('posts.create-post-modal')
        ->assertOk()
        ->assertViewIs('livewire.posts.create-post-modal')
        ->assertSee(__('Create New Post'));
});

it('stores uploaded image to temp directory and dispatches openModal with filters-modal', function () {
    Storage::fake('public');

    $user = User::factory()->create();
    $this->actingAs($user);

    $file = UploadedFile::fake()->image('test_photo.jpg');

    Livewire::test('posts.create-post-modal')
        ->set('image', $file)
        ->call('save_temp')
        ->assertDispatched('openModal', function (string $event, array $params) {
            $component = $params[0] ?? null;
            $arguments = $params[1] ?? [];

            expect($component)->toBe('posts.filters-modal');
            expect($arguments)->toHaveKey('image');
            expect($arguments['image'])->toStartWith('temp/');

            Storage::disk('public')->assertExists($arguments['image']);

            return true;
        });
});
