<?php

namespace Concrete\Core\Board\Command;

use Concrete\Core\Application\Application;
use Concrete\Core\Board\DataSource\Driver\Manager as BoardDataSourceManager;
use Concrete\Core\Board\DataSource\Driver\NotifierAwareDriverInterface;
use Doctrine\ORM\EntityManager;

abstract class AbstractUpdateBoardInstanceCommandHandler
{
    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var Application
     */
    protected $app;

    /**
     * @var BoardDataSourceManager
     */
    protected $boardDataSourceManager;

    public function __construct(Application $app, BoardDataSourceManager $boardDataSourceManager, EntityManager $entityManager)
    {
        $this->app = $app;
        $this->boardDataSourceManager = $boardDataSourceManager;
        $this->entityManager = $entityManager;
    }

    protected function getInstances(AbstractUpdateBoardInstanceCommand $command): array
    {
        $driver = $this->boardDataSourceManager->driver($command->getDriver());
        if ($driver instanceof NotifierAwareDriverInterface) {
            $notifier = $driver->getBoardInstanceNotifier();
            return $notifier->findBoardInstancesThatMayContainObject($command->getObject());
        }
    }
}
