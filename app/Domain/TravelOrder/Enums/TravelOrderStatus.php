<?php

namespace App\Domain\TravelOrder\Enums;

enum TravelOrderStatus: string
{
    case REQUESTED = 'requested';
    case APPROVED = 'approved';
    case CANCELLED = 'cancelled';

    public function label(): string
    {
        return match($this) {
            self::REQUESTED => 'Solicitado',
            self::APPROVED => 'Aprovado',
            self::CANCELLED => 'Cancelado',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::REQUESTED => 'warning',
            self::APPROVED => 'success',
            self::CANCELLED => 'danger',
        };
    }
} 