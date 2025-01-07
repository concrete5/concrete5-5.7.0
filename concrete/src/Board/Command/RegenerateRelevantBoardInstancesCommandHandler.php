<?php

namespace Concrete\Core\Board\Command;

class RegenerateRelevantBoardInstancesCommandHandler extends AbstractUpdateBoardInstanceCommandHandler
{
    public function __invoke(RegenerateRelevantBoardInstancesCommand $command)
    {
        foreach ($this->getInstances($command) as $instance) {
            $regenerateCommand = new RegenerateBoardInstanceCommand();
            $regenerateCommand->setDefer(true);
            $regenerateCommand->setInstance($instance);
            $this->app->executeCommand($regenerateCommand);
        }
    }

}
