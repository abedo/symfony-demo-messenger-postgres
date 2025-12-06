<?php

namespace App\Command;

use App\Message\SmsNotification;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsCommand(
    name: 'app:send-sms',
    description: 'Creates users and stores them in the database',
)]
class SmsNotificationCommand extends Command
{
    private SymfonyStyle $io;

    public function __construct(
        private readonly MessageBusInterface $bus,
    ) {
        parent::__construct();
    }

    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        // SymfonyStyle is an optional feature that Symfony provides so you can
        // apply a consistent look to the commands of your application.
        // See https://symfony.com/doc/current/console/style.html
        $this->io = new SymfonyStyle($input, $output);
    }

    public function __invoke(): int
    {
        // will cause the SmsNotificationHandler to be called
        $this->bus->dispatch(new SmsNotification('Look! I created a message!'));

        $this->io->title('SmsNotificationCommand');

        return Command::SUCCESS;
    }
}
