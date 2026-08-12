<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
    return [
        'name' => ['required', 'string', 'max:255'],
        'email' => [
            'required',
            'string',
            'lowercase',
            'email',
            'max:255',
            Rule::unique(User::class)->ignore($this->user()->id),
        ],
        'photo' => ['nullable', 'image', 'max:2048'],
        'phone' => ['nullable', 'string', 'max:20'],
        'whatsapp' => ['nullable', 'boolean'],
        'birthdate' => ['nullable', 'date'],
        'cpf' => ['nullable', 'string', 'max:14'],
        'cep' => ['nullable', 'string', 'max:10'],
        'street' => ['nullable', 'string', 'max:255'],
        'number' => ['nullable', 'string', 'max:10'],
        'complement' => ['nullable', 'string', 'max:255'],
        'city' => ['nullable', 'string', 'max:255'],
        'neighborhood' => ['nullable', 'string', 'max:255'],
        'state' => ['nullable', 'string', 'max:2'],
        'notifications_enabled' => ['nullable', 'boolean'],
    ];
}
}
