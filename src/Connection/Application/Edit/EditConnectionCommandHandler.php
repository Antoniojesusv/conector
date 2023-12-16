<?php

declare(strict_types=1);
namespace App\Connection\Application\Edit;

use App\Connection\Application\ConnectionFactory;
use App\Connection\Domain\ConnectionId;
use App\Connection\Domain\User;
use App\Connection\Infrastructure\Persistance\Pdo\ConnectionRepository;
use App\Shared\Domain\Bus\Command\Contract\Command;
use App\Shared\Domain\Bus\Command\Contract\CommandHandler;
use Ramsey\Uuid\Uuid;

final class EditConnectionCommandHandler implements CommandHandler
{
    public function __construct(
        private ConnectionRepository $connectionRepository,
        private ConnectionFactory $connectionFactory
    ) {
        $this->connectionRepository = $connectionRepository;
        $this->connectionFactory = $connectionFactory;
    }

    /**
     * Summary of __invoke
     * @param App\Connection\Application\Edit\EditConnectionCommand $command
     * @return mixed
     */
    public function __invoke(Command $command): mixed
    {
        $connection = $this->connectionFactory->create(
            Uuid::uuid4()->toString(),
            $command->user(),
            $command->password(),
            $command->address(),
            $command->port(),
            $command->databaseName(),
            $command->Type()
        );

        $this->connectionRepository->update($connection);

        return null;
    }
}