<?php
namespace Concrete\Controller\SinglePage\Dashboard\System\Boards;

use Concrete\Core\Page\Controller\DashboardPageController;

class Settings extends DashboardPageController
{

    public function view()
    {
        $config = $this->app->make('config');
        $logBoardInstances = (int) $config->get('concrete.log.boards.instances');
        $this->set('logBoardInstances', $logBoardInstances);
    }

    public function save()
    {
        if (!$this->token->validate('save')) {
            $this->error->add($this->token->getErrorMessage());
        }
        if (!$this->error->has()) {
            $config = $this->app->make('config');
            $logBoardInstances = $this->request->request->getBoolean('log_board_instances');
            $config->save('concrete.log.boards.instances', $logBoardInstances);
            $this->flash('success', t('Board instance logging configuration saved.'));
            return $this->buildRedirect($this->action('view'));
        }
        $this->view();
    }
}
