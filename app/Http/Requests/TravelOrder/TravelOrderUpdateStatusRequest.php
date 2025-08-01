<?php

namespace App\Http\Requests\TravelOrder;

use App\Http\Requests\BaseRequest;

class TravelOrderUpdateStatusRequest extends BaseRequest
{
    protected array $allowedFields = [
        'status'
    ];

    protected array $customMessages = [
        'status.required' => 'O campo status é obrigatório.',
        'status.in' => 'O status deve ser "approved" ou "cancelled".',
    ];

    public function rules(): array
    {
        return [
            'status' => 'required|in:approved,cancelled',
        ];
    }
} 