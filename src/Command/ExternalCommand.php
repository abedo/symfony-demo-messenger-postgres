<?php

namespace App\Command;

use App\Message\RunExternalCommand;
use App\Message\SmsNotification;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsCommand(
    name: 'app:external-command',
    description: 'Dispatches RunExternalCommand with commandName param',
)]
class ExternalCommand extends Command
{
    private SymfonyStyle $io;

    public function __construct(
        private readonly MessageBusInterface $bus,
    ) {
        parent::__construct();
    }

    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        $this->io = new SymfonyStyle($input, $output);
    }

    public function __invoke(): int
    {
        // will cause the RunExternalCommandHandler to be called
        $this->bus->dispatch(new RunExternalCommand('messenger:stats'));

        $this->io->title('ExternalCommand');

        return Command::SUCCESS;
    }
}
