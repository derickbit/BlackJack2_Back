<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\User;
use Illuminate\Validation\Rule;

class UserUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $target = $this->route('user');

        return $target instanceof User && $this->user()?->is($target);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $passwordRules = ['bail', 'required', 'string', 'max:255'];

        // The published name-edit form sends the existing password in `password`.
        // Only the password-change form sends `current_password` plus a new password.
        // Without that separate confirmation, do not allow replacing the password.
        if (!$this->exists('current_password')) {
            $passwordRules[] = 'current_password:sanctum';
        }

        return [
            "name" => "required|string|max:255",
            "email" => ['required', 'email', 'max:255', Rule::unique('users')->ignore($this->route('user'))],
            'password' => $passwordRules,
            'current_password' => ['bail', 'sometimes', 'required', 'string', 'max:255', 'current_password:sanctum'],
        ];
    }

}
