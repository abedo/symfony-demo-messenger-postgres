<?php

namespace App\Message;

class ProcessLineMessage
{
    public function __construct(
        private string $paymentTransactionId,
        private string $amount = '0',
    ) {
    }

    public function getPaymentTransactionId(): string
    {
        return $this->paymentTransactionId;
    }

    public function getAmount(): string
    {
        return $this->amount;
    }
}
