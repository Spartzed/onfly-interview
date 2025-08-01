<?php

namespace App\Http\Requests\TravelOrder;

use App\Http\Requests\BaseRequest;

class TravelOrderStoreRequest extends BaseRequest
{
    protected array $allowedFields = [
        'requester_name',
        'destination',
        'departure_date',
        'return_date'
    ];

    protected array $customMessages = [
        'requester_name.required' => 'O campo nome do solicitante é obrigatório.',
        'requester_name.string' => 'O campo nome do solicitante deve ser uma string.',
        'requester_name.max' => 'O campo nome do solicitante não pode ter mais que 255 caracteres.',
        'destination.required' => 'O campo destino é obrigatório.',
        'destination.string' => 'O campo destino deve ser uma string.',
        'destination.max' => 'O campo destino não pode ter mais que 255 caracteres.',
        'departure_date.required' => 'O campo data de ida é obrigatório.',
        'departure_date.date' => 'O campo data de ida deve ser uma data válida.',
        'departure_date.after' => 'A data de ida deve ser posterior a hoje.',
        'return_date.required' => 'O campo data de volta é obrigatório.',
        'return_date.date' => 'O campo data de volta deve ser uma data válida.',
        'return_date.after' => 'A data de volta deve ser posterior à data de ida.',
    ];

    public function rules(): array
    {
        return [
            'requester_name' => 'required|string|max:255',
            'destination' => 'required|string|max:255',
            'departure_date' => 'required|date|after:today',
            'return_date' => 'required|date|after:departure_date',
        ];
    }
} 