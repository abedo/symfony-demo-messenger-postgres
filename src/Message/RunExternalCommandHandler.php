<?php

namespace App\Message;

use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class RunExternalCommandHandler
{
    public function __construct(
        private LoggerInterface $logger,
        private KernelInterface $kernel
    ) {
    }

    public function __invoke(RunExternalCommand $message)
    {
        $application = new Application($this->kernel);
        $application->setAutoExit(false);

        $input = new ArrayInput([
            'command' => $message->commandName,
        ]);

        $output = new BufferedOutput();
        $exitCode = $application->run($input, $output);

        $this->logger->info('MessageDebug', [
            'commandName' => $message->commandName,
            'exitCode' => $exitCode,
            'content' => $output->fetch(),
        ]);
    }
}
