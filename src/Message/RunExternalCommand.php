<?php

namespace App\Message;

class RunExternalCommand
{
    public function __construct(
        public string $commandName,
        public array $arguments = []
    ) {
    }
}
