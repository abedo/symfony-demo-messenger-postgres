<?php

// src/MessageHandler/SmsNotificationHandler.php
namespace App\Message;

use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class SmsNotificationHandler
{
    public function __construct(
        private LoggerInterface $logger
    ) {
    }

    public function __invoke(SmsNotification $message)
    {
        // ... do some work - like sending an SMS message!
        $this->logger->info('MessageDebug', [
            'getContent' => $message->getContent(),
        ]);

//        throw new \RuntimeException('test retry');
    }
}
