<?php

namespace App\Listeners;

use App\Events\OrderCompletedEvent;
use App\Mail\AdminOrdersMarkDown;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class NotifyAdminListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(OrderCompletedEvent $event)
    {
        $ordere = $event->ordere;
        Mail::to(env('MAIL_FROM_ADDRESS'))->send(new AdminOrdersMarkDown($ordere));
    }
}
