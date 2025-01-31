<?php

namespace Modules\LMS\app\Events;

use Illuminate\Queue\SerializesModels;
use Modules\PayStream\app\Models\PsPayments;

class PaymentCreatedEvent
{
    use SerializesModels;

    public PsPayments $payment;
    /**
     * Create a new event instance.
     */
    public function __construct($payment)
    {
        $this -> payment = $payment;
    }

    /**
     * Get the channels the event should be broadcast on.
     */
    public function broadcastOn(): array
    {
        return [];
    }
}
