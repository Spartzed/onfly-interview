<?php

namespace App\Domain\TravelOrder\Notifications;

use App\Domain\TravelOrder\Entities\TravelOrder;
use App\Domain\TravelOrder\Enums\TravelOrderStatus;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TravelOrderStatusNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private TravelOrder $travelOrder,
        private TravelOrderStatus $newStatus
    ) {}

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        $statusLabel = $this->newStatus->label();
        
        return (new MailMessage)
            ->subject("Pedido de Viagem {$statusLabel}")
            ->greeting("Olá {$notifiable->name}!")
            ->line("Seu pedido de viagem foi {$statusLabel}.")
            ->line("Detalhes do pedido:")
            ->line("- ID do Pedido: {$this->travelOrder->order_id}")
            ->line("- Destino: {$this->travelOrder->destination}")
            ->line("- Data de Ida: " . $this->travelOrder->departure_date->format('d/m/Y'))
            ->line("- Data de Volta: " . $this->travelOrder->return_date->format('d/m/Y'))
            ->line("- Status: {$statusLabel}")
            ->action('Ver Detalhes', url('/travel-orders/' . $this->travelOrder->id))
            ->line('Obrigado por usar nosso sistema!');
    }

    public function toArray($notifiable): array
    {
        return [
            'travel_order_id' => $this->travelOrder->id,
            'order_id' => $this->travelOrder->order_id,
            'status' => $this->newStatus->value,
            'status_label' => $this->newStatus->label(),
            'destination' => $this->travelOrder->destination,
            'message' => "Seu pedido de viagem foi {$this->newStatus->label()}."
        ];
    }
} 