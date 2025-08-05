<?php

namespace App\Domain\TravelOrder\Entities;

use App\Domain\User\Entities\User;
use App\Domain\TravelOrder\Enums\TravelOrderStatus;
use App\Domain\TravelOrder\Events\TravelOrderStatusChanged;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TravelOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'requester_name',
        'destination',
        'departure_date',
        'return_date',
        'status',
        'user_id',
    ];

    protected $casts = [
        'departure_date' => 'date',
        'return_date' => 'date',
        'status' => TravelOrderStatus::class,
    ];

    protected $dispatchesEvents = [
        'updated' => TravelOrderStatusChanged::class,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function canBeCancelled(): bool
    {
        return $this->status === TravelOrderStatus::APPROVED;
    }

    public function canBeUpdatedBy(User $user): bool
    {
        return $user->isAdmin() || $this->user_id === $user->id;
    }

    public function canChangeStatus(User $user): bool
    {
        return $user->isAdmin();
    }

    public function updateStatus(TravelOrderStatus $status): void
    {
        $this->status = $status;
        $this->save();
    }

    protected static function newFactory()
    {
        return \Database\Factories\TravelOrderFactory::new();
    }
} 