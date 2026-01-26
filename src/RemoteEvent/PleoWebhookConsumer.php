<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\RemoteEvent;

use Psr\Log\LoggerInterface;
use Symfony\Component\RemoteEvent\Attribute\AsRemoteEventConsumer;
use Symfony\Component\RemoteEvent\Consumer\ConsumerInterface;
use Symfony\Component\RemoteEvent\RemoteEvent;

#[AsRemoteEventConsumer('pleo')]
final class PleoWebhookConsumer implements ConsumerInterface
{
    public function __construct(private LoggerInterface $logger)
    {
    }

    public function consume(RemoteEvent $event): void
    {
        $data = $event->getPayload();

        $this->logger->info('PleoWebhookConsumer', [
            'data' => $data,
            'getName' => $event->getName(),
            'getId' => $event->getId(),
        ]);

        dump('Otrzymano webhook dla zamówienia: ' . $event->getId());
//        throw new \RuntimeException('Celowy błąd do testowania failure_transport!');
    }
}
