<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    public function rules(): array
    {
        if ($this->input('_form') === 'akun') {
            return [
                'email' => [
                    'required', 'string', 'lowercase', 'email', 'max:255',
                    Rule::unique(User::class)->ignore($this->user()->id),
                ],
                'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            ];
        }

        return [
            'name' => ['required', 'string', 'max:255'],
            'nim'  => [
                'nullable', 'string', 'max:50',
                Rule::unique(User::class)->ignore($this->user()->id),
            ],
        ];
    }
}
