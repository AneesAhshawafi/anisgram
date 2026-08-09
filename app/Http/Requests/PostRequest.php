<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

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
            "description" => "required",
            "image" => ["required", "mimes:jpeg,jpg,png,gif"]
        ];
    }
    public function messages(): array
    {
        return [
            "description.required" => "Decription is required",
            "image.required" => "Please upload your image",
            "image.mimes" => "The file you uploaded is not supported,
            supported files are jpeg,jpg,png, or gif"
        ];
    }
}
