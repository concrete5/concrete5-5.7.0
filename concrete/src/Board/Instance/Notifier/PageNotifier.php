<?php

namespace Concrete\Core\Board\Instance\Notifier;

use Concrete\Core\Entity\Board\DataSource\Configuration\CalendarEventConfiguration;
use Concrete\Core\Entity\Board\DataSource\Configuration\PageConfiguration;
use Concrete\Core\Entity\Board\Instance;
use Concrete\Core\Entity\Site\Site;
use Concrete\Core\Page\Page;
use Concrete\Core\Page\Search\Field\Field\PageTypeField;
use Concrete\Core\Page\Type\Type;
use Doctrine\ORM\EntityManager;

defined('C5_EXECUTE') or die("Access Denied.");

class PageNotifier extends AbstractNotifier
{

    protected function filterByPageType(array $instances, ?Type $pageType = null): array
    {
        $return = [];
        foreach ($instances as $instance) {
            $includeInstance = true;
            $board = $instance->getBoard();
            if ($board) {
                foreach ($board->getDataSources() as $configuredDataSource) {
                    $configuration = $configuredDataSource->getConfiguration();
                    if ($configuration instanceof PageConfiguration) {
                        $query = $configuration->getQuery();
                        if ($query) {
                            foreach ($query->getFields() as $field) {
                                if ($field instanceof PageTypeField) {
                                    if ($field->getData('ptID') == $pageType->getPageTypeID()) {
                                        $includeInstance = true;
                                    } else {
                                        $includeInstance = false;
                                    }
                                }
                            }
                        }
                    }
                }
            }
            if ($includeInstance) {
                $return[] = $instance;
            }
        }
        return $return;
    }

    /**
     * @param Page $object
     * @return array
     */
    public function findBoardInstancesThatMayContainObject($object): array
    {
        $site = $object->getSite();
        // Note - due to the way filtering works the combinations are practically infinite, so we can't
        // necessarily check all the ways this particular object might fit into a board. So instead
        // Let's pick the ones we've used and the ones that are most common – whether the page is part of a
        // board instance that is in the same site, and whether the board is filtering by page type, and, if so
        // whether that page type is the same as the board data source configuration.
        $instances = $this->filterBySite($site);
        $instances = $this->filterByHasConfiguration($instances, PageConfiguration::class);
        $instances = $this->filterByPageType($instances, $object->getPageTypeObject());
        return $instances;
    }
}

