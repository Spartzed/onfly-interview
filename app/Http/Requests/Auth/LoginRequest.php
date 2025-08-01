<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\BaseRequest;

class LoginRequest extends BaseRequest
{
    protected array $allowedFields = [
        'email',
        'password'
    ];

    protected array $customMessages = [
        'email.required' => 'O campo email é obrigatório.',
        'email.email' => 'O campo email deve ser um email válido.',
        'password.required' => 'O campo senha é obrigatório.',
    ];

    public function rules(): array
    {
        return [
            'email' => 'required|email',
            'password' => 'required',
        ];
    }
} 