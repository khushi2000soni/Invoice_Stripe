<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class InvoiceUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;


    // public $invoice;

    // public function __construct($invoice)
    // {
    //     $this->invoice = $invoice;
    // }

    public function broadcastOn()
    {
        return new Channel('invoices');
    }

    public function broadcastAs()
    {
        return 'invoice-updated';
    }
}
