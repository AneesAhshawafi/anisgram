<?php

use App\Livewire\Posts\FiltersModal;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

uses(RefreshDatabase::class);

/*
|--------------------------------------------------------------------------
| Livewire: posts.filters-modal Component Tests
|--------------------------------------------------------------------------
*/

beforeEach(function () {
    // Ensure public storage directory exists for real file operations with Intervention Image
    File::ensureDirectoryExists(storage_path('app/public/temp'));
    File::ensureDirectoryExists(storage_path('app/public/posts'));
});

afterEach(function () {
    // Clean up test files in public disk
    Storage::disk('public')->deleteDirectory('temp');
    Storage::disk('public')->deleteDirectory('posts');
});

/**
 * Helper to create a genuine test JPEG image on public storage disk.
 */
function createTestImage(string $relativePath = 'temp/sample_test.jpg'): string
{
    $fullPath = storage_path('app/public/'.$relativePath);
    File::ensureDirectoryExists(dirname($fullPath));

    $img = imagecreatetruecolor(100, 100);
    imagejpeg($img, $fullPath);
    imagedestroy($img);

    return $relativePath;
}

it('mounts filters-modal component with image and initializes filtered_image and temp_images', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $tempImage = createTestImage('temp/initial_img.jpg');

    Livewire::test('posts.filters-modal', ['image' => $tempImage])
        ->assertOk()
        ->assertSet('image', $tempImage)
        ->assertSet('filtered_image', asset('storage/'.$tempImage))
        ->assertSet('temp_images', [$tempImage])
        ->assertViewIs('livewire.posts.filters-modal');
});

it('returns 5xl for modalMaxWidth and true for dispatchCloseEvent', function () {
    expect(FiltersModal::modalmaxWidth())->toBe('5xl');
    expect(FiltersModal::dispatchCloseEvent())->toBeTrue();
});

it('retrieves currentUser via computed property', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $tempImage = createTestImage('temp/user_check.jpg');

    $component = Livewire::test('posts.filters-modal', ['image' => $tempImage]);

    expect($component->get('currentUser')->id)->toBe($user->id);
});

it('resets filtered_image to original image when original_filter is called', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $tempImage = createTestImage('temp/original_test.jpg');

    $component = Livewire::test('posts.filters-modal', ['image' => $tempImage]);

    // Apply another filter first
    $component->call('clarendon_filter');
    expect($component->get('filtered_image'))->not->toBe(asset('storage/'.$tempImage));

    // Reset back to original
    $component->call('original_filter');
    expect($component->get('filtered_image'))->toBe(asset('storage/'.$tempImage));
});

it('applies clarendon filter and generates a new temp image', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $tempImage = createTestImage('temp/clarendon_test.jpg');

    $component = Livewire::test('posts.filters-modal', ['image' => $tempImage])
        ->call('clarendon_filter');

    $filteredImage = $component->get('filtered_image');
    $tempImages = $component->get('temp_images');

    expect($filteredImage)->toContain('/storage/temp/');
    expect(count($tempImages))->toBe(2);

    $latestTemp = end($tempImages);
    expect(Storage::disk('public')->exists($latestTemp))->toBeTrue();
});

it('applies gingham filter and generates a new temp image', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $tempImage = createTestImage('temp/gingham_test.jpg');

    $component = Livewire::test('posts.filters-modal', ['image' => $tempImage])
        ->call('gingham_filter');

    $filteredImage = $component->get('filtered_image');
    $tempImages = $component->get('temp_images');

    expect($filteredImage)->toContain('/storage/temp/');
    expect(count($tempImages))->toBe(2);

    $latestTemp = end($tempImages);
    expect(Storage::disk('public')->exists($latestTemp))->toBeTrue();
});

it('applies moon filter and generates a new temp image', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $tempImage = createTestImage('temp/moon_test.jpg');

    $component = Livewire::test('posts.filters-modal', ['image' => $tempImage])
        ->call('moon_filter');

    $filteredImage = $component->get('filtered_image');
    $tempImages = $component->get('temp_images');

    expect($filteredImage)->toContain('/storage/temp/');
    expect(count($tempImages))->toBe(2);

    $latestTemp = end($tempImages);
    expect(Storage::disk('public')->exists($latestTemp))->toBeTrue();
});

it('applies perpetua filter and generates a new temp image', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $tempImage = createTestImage('temp/perpetua_test.jpg');

    $component = Livewire::test('posts.filters-modal', ['image' => $tempImage])
        ->call('perpetua_filter');

    $filteredImage = $component->get('filtered_image');
    $tempImages = $component->get('temp_images');

    expect($filteredImage)->toContain('/storage/temp/');
    expect(count($tempImages))->toBe(2);

    $latestTemp = end($tempImages);
    expect(Storage::disk('public')->exists($latestTemp))->toBeTrue();
});

it('fails validation when publishing a post with empty description', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $tempImage = createTestImage('temp/fail_val.jpg');

    Livewire::test('posts.filters-modal', ['image' => $tempImage])
        ->set('description', '')
        ->call('publish')
        ->assertHasErrors(['description' => 'required']);

    $this->assertDatabaseCount('posts', 0);
});

it('publishes post successfully, creates database record, moves image, cleans up temp files and redirects', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $tempImage = createTestImage('temp/publish_source.jpg');

    $component = Livewire::test('posts.filters-modal', ['image' => $tempImage])
        ->call('moon_filter')
        ->set('description', 'My filtered photo publication')
        ->call('publish')
        ->assertHasNoErrors()
        ->assertDispatched('closeModal');

    $this->assertDatabaseHas('posts', [
        'user_id' => $user->id,
        'description' => 'My filtered photo publication',
    ]);

    $post = Post::where('user_id', $user->id)->first();
    expect($post)->not->toBeNull();
    expect(strlen($post->slug))->toBe(10);
    expect($post->image)->toStartWith('posts/');
    expect(Storage::disk('public')->exists($post->image))->toBeTrue();

    // Verify unused initial temp image was cleaned up
    expect(Storage::disk('public')->exists('temp/publish_source.jpg'))->toBeFalse();
});

it('cleans up all temp images when modalClosed event is received', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $tempImage = createTestImage('temp/to_be_closed.jpg');

    $component = Livewire::test('posts.filters-modal', ['image' => $tempImage])
        ->call('clarendon_filter');

    $tempImages = $component->get('temp_images');
    expect(count($tempImages))->toBe(2);

    // Verify files exist before closing
    foreach ($tempImages as $img) {
        expect(Storage::disk('public')->exists($img))->toBeTrue();
    }

    // Trigger modalClosed event
    $component->dispatch('modalClosed');

    // Verify all temp files are deleted
    foreach ($tempImages as $img) {
        expect(Storage::disk('public')->exists($img))->toBeFalse();
    }
});
