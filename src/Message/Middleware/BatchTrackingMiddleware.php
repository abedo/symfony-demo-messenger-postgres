<?php

namespace App\Message\Middleware;

use App\Message\Stamp\BatchStamp;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\Middleware\StackInterface;

class BatchTrackingMiddleware implements MiddlewareInterface
{
    public function handle(Envelope $envelope, StackInterface $stack): Envelope
    {
        // 1. Pozwól wiadomości się przetworzyć (wykonaj handler)
        try {
            $envelope = $stack->next()->handle($envelope, $stack);
        } finally {
            // 2. Po zakończeniu (sukces lub błąd), sprawdź czy jest BatchStamp
            $batchStamp = $envelope->last(BatchStamp::class);

            if ($batchStamp) {
//                $this->repository->incrementAndCheckFinished($batchStamp->batchId);
            }
        }

        return $envelope;
    }
}
