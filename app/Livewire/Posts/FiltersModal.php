<?php

namespace App\Livewire\Posts;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use LivewireUI\Modal\ModalComponent;

class FiltersModal extends ModalComponent
{
    public $image;

    public $filters = [
        'Original',
        'Clarendon',
        'Gingham',
        'Moon',
        'Perpetua',
    ];

    public $filtered_image;

    public $temp_images = [];

    public $description;

    public static function modalmaxWidth(): string
    {
        return '5xl';
    }

    #[Computed]
    public function currentUser()
    {
        return Auth::user();
    }

    private function normalizeRelativePath(string $path): string
    {
        $pathOnly = parse_url($path, PHP_URL_PATH) ?? $path;

        return ltrim(str_replace('/storage/', '', $pathOnly), '/');
    }

    public function mount($image)
    {
        $this->image = $image;
        $relativePath = $this->normalizeRelativePath($image);
        $this->filtered_image = asset('storage/'.$relativePath);

        $this->add_temp_image($relativePath);
    }

    #[On('add_temp_image')]
    public function add_temp_image($image)
    {
        $relativePath = $this->normalizeRelativePath($image);

        if (! in_array($relativePath, $this->temp_images)) {
            $this->temp_images[] = $relativePath;
        }
    }

    private function applyFilter(callable $callback): void
    {
        $relativePath = $this->normalizeRelativePath($this->image);
        $sourcePath = storage_path('app/public/'.$relativePath);

        $tempDirectory = storage_path('app/public/temp');
        File::ensureDirectoryExists($tempDirectory);

        $fileName = Str::random(30).'.jpeg';
        $destinationPath = $tempDirectory.DIRECTORY_SEPARATOR.$fileName;

        $manager = ImageManager::usingDriver(Driver::class);
        $image = $manager->decodePath($sourcePath);

        $callback($image);

        $image->save($destinationPath);

        $this->filtered_image = asset('storage/temp/'.$fileName);
        $this->add_temp_image('temp/'.$fileName);
    }

    public function original_filter()
    {
        $relativePath = $this->normalizeRelativePath($this->image);
        $this->filtered_image = asset('storage/'.$relativePath);
    }

    public function clarendon_filter()
    {
        $this->applyFilter(function ($image) {
            $image->brightness(20)->contrast(15);
        });
    }

    public function gingham_filter()
    {
        $this->applyFilter(function ($image) {
            $image->brightness(10)->contrast(-10);
        });
    }

    public function moon_filter()
    {
        $this->applyFilter(function ($image) {
            $image->grayscale()->contrast(15)->brightness(5);
        });
    }

    public function perpetua_filter()
    {
        $this->applyFilter(function ($image) {
            $image->brightness(5)->contrast(10);
        });
    }

    public function publish()
    {
        $this->validate([
            'description' => 'required',
        ]);

        $sourceRelativePath = $this->normalizeRelativePath($this->filtered_image);
        $post_image = 'posts/'.Str::random(30).'.jpeg';

        Storage::disk('public')->move($sourceRelativePath, $post_image);

        foreach ($this->temp_images as $tempImg) {
            if ($tempImg !== $sourceRelativePath && Storage::disk('public')->exists($tempImg)) {
                Storage::disk('public')->delete($tempImg);
            }
        }

        $slug = Str::random(10);
        $post = $this->currentUser->posts()->create([
            'description' => $this->description,
            'slug' => $slug,
            'image' => $post_image,
        ]);

        $this->forceClose()->closeModal();

        return redirect('/p/'.$slug)->with('success', 'Post created successfully!');
    }

    public static function dispatchCloseEvent(): bool
    {
        return true;
    }

    #[On('modalClosed')]
    public function delete_temp_images()
    {
        foreach ($this->temp_images as $tempImg) {
            if (Storage::disk('public')->exists($tempImg)) {
                Storage::disk('public')->delete($tempImg);
            }
        }
    }

    public function render()
    {
        return view('livewire.posts.filters-modal');
    }
}
