<?php

namespace App\Message;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class RunExternalCommandHandler
{
    public function __construct(private KernelInterface $kernel) {}

    public function __invoke(RunExternalCommand $message)
    {
        $application = new Application($this->kernel);
        $application->setAutoExit(false);

        $input = new ArrayInput(array_merge(
            ['command' => $message->commandName],
            $message->arguments
        ));

        $output = new BufferedOutput();
        $application->run($input, $output);

        // Opcjonalnie: logowanie wyniku $output->fetch()
    }
}
