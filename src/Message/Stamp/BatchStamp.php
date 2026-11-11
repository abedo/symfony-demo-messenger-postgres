<?php

namespace App\Message\Stamp;

use Symfony\Component\Messenger\Stamp\StampInterface;

class BatchStamp implements StampInterface
{
    public function __construct(public readonly string $batchId) {}
}
