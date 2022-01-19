<?php

namespace App\Events;

use App\Mail\AmbassadorOrdersMarkDown;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\Ordere;
use Illuminate\Support\Facades\Mail;

class OrderCompletedEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Ordere $ordere;

    public function __construct(Ordere $ordere)
    {
        $this->ordere = $ordere;
    }
}
