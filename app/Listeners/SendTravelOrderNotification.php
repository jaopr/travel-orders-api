<?php

namespace App\Listeners;

use App\Events\TravelOrderStatusChanged;
use App\Notifications\TravelOrderStatusNotification;

class SendTravelOrderNotification  {
    
    public function handle(TravelOrderStatusChanged $event): void {
        $order = $event->travelOrder;
        $order->user->notify(new TravelOrderStatusNotification($order));
    }
}