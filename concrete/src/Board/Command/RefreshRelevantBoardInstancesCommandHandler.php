<?php

namespace Concrete\Core\Board\Command;

class RefreshRelevantBoardInstancesCommandHandler extends AbstractUpdateBoardInstanceCommandHandler
{
    public function __invoke(RefreshRelevantBoardInstancesCommand $command): void
    {
        foreach ($this->getInstances($command) as $instance) {
            $refreshCommand = new RefreshBoardInstanceCommand();
            $refreshCommand->setInstance($instance);
            $this->app->executeCommand($refreshCommand);
        }
    }
    

}
