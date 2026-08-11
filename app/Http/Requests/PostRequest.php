<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

use Illuminate\Support\Facades\Route;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use App\Models\Post;
use App\Models\Comment;
use Illuminate\Support\Str;

class PostRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'description' => 'required',
            'image' => [
                $this->isMethod('post') ? 'required' : 'nullable',
                'mimes:jpeg,jpg,png,gif',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'description.required' => 'Decription is required',
            'image.mimes' => 'The file you uploaded is not supported,
            supported files are jpeg,jpg,png, or gif',
        ];
    }
}
