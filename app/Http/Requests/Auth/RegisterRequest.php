<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\BaseRequest;

class RegisterRequest extends BaseRequest
{
    protected array $allowedFields = [
        'name',
        'email',
        'password',
        'password_confirmation'
    ];

    protected array $customMessages = [
        'name.required' => 'O campo nome é obrigatório.',
        'name.string' => 'O campo nome deve ser uma string.',
        'name.max' => 'O campo nome não pode ter mais que 255 caracteres.',
        'email.required' => 'O campo email é obrigatório.',
        'email.email' => 'O campo email deve ser um email válido.',
        'email.unique' => 'Este email já está sendo utilizado.',
        'password.required' => 'O campo senha é obrigatório.',
        'password.min' => 'A senha deve ter pelo menos 8 caracteres.',
        'password.confirmed' => 'A confirmação de senha não confere.',
    ];

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ];
    }
} 