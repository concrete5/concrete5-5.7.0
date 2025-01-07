<?php

namespace Concrete\Core\Board\Instance\Notifier;

use Concrete\Core\Entity\Board\DataSource\Configuration\CalendarEventConfiguration;
use Concrete\Core\Entity\Calendar\CalendarEvent;

defined('C5_EXECUTE') or die("Access Denied.");

class CalendarEventNotifier extends AbstractNotifier
{

    /**
     * @param CalendarEvent $object
     * @return array
     */
    public function findBoardInstancesThatMayContainObject($object): array
    {
        $site = $object->getCalendar()->getSite();
        $instances = $this->filterBySite($site);
        $instances = $this->filterByHasConfiguration($instances, CalendarEventConfiguration::class);
        return $instances;
    }
}

