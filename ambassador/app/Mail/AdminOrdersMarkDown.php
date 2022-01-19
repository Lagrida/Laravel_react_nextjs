<?php

namespace App\Mail;

use App\Models\Ordere;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AdminOrdersMarkDown extends Mailable
{
    use Queueable, SerializesModels;

    public Ordere $ordere;

    public function __construct(Ordere $ordere)
    {
        $this->ordere = $ordere;
    }
    public function build()
    {
        $subject = 'An order is completed';
        return $this->subject($subject)->markdown('mails.admin-orders');
    }
}
