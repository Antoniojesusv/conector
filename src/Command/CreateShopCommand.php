<?php

namespace App\Command;

use App\Shared\Infrastructure\Bus\Command\CommandBus;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:create-shop',
    description: 'Creates a new shop.',
    hidden: false,
    aliases: ['app:add-shop']
)]
class CreateShopCommand extends Command
{
    private CommandBus $commandBus;
    protected static $defaultDescription = 'Creates a new shop.';

    public function __construct(CommandBus $commandBus)
    {
        $this->commandBus = $commandBus;

        // you must call the parent constructor
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $command = new \App\Shop\Application\Create\CreateShopCommand(
            $input->getArgument('name'),
            $input->getArgument('rate'),
            $input->getArgument('store')
        );
        $this->commandBus->dispatch($command);

        $output->writeln("Successfully created shop. \xF0\x9F\x98\x8A");
        return Command::SUCCESS;
    }

    protected function configure(): void
    {
        $this
            ->setHelp('This command allows you to create a shop')
            ->addArgument('name', InputArgument::REQUIRED, 'The shop name')
            ->addArgument('rate', InputArgument::REQUIRED, 'The rate to filter the synchronization')
            ->addArgument('store', InputArgument::REQUIRED, 'The store to filter the synchronization');
        ;
    }
}