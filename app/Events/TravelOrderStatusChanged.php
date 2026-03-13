<?php

namespace App\Events;

use App\Models\TravelOrder;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TravelOrderStatusChanged {
    use Dispatchable, SerializesModels;

    public function __construct(
        public TravelOrder $travelOrder
    ) {}
}