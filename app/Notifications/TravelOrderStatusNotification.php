<?php

namespace App\Notifications;

use App\Models\TravelOrder;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class TravelOrderStatusNotification extends Notification {
    public function __construct(
        public TravelOrder $travelOrder
    ) {}

    public function via(object $notifiable): array {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage {
        $status = $this->travelOrder->status === 'approved' ? 'aprovado' : 'cancelado';

        return (new MailMessage)
            ->subject("Pedido de viagem {$status}")
            ->line("Olá {$notifiable->name},")
            ->line("Seu pedido de viagem para {$this->travelOrder->destination} foi {$status}.")
            ->line("Data de ida: {$this->travelOrder->departure_date->format('d/m/Y')}")
            ->line("Data de volta: {$this->travelOrder->return_date->format('d/m/Y')}");
    }

    public function toArray(object $notifiable): array {
        return [
            'travel_order_id' => $this->travelOrder->id,
            'destination' => $this->travelOrder->destination,
            'status' => $this->travelOrder->status,
        ];
    }
}