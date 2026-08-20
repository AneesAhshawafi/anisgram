<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class UpdateUserProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // $user = User::find($this->route('user')->id);
        // $user = $this->route('user');
        // return Gate::allows('edit_update_profile',$user);
        // $this->user : returns the user whose profile is being edited
        // $this->user() : returns the current user who is loged in

        return Gate::allows('edit_update_profile', $this->user);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'username' => ['required', Rule::unique('users')->ignore($this->user())],
            'bio' => ['nullable'],
            'name' => ['required'],
            'image' => 'image',
            'email' => ['required', 'email'],
            'password' => ['min:8', 'nullable', 'confirmed'],

        ];
    }
}
