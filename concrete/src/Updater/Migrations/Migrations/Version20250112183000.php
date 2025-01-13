<?php

declare(strict_types=1);

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;

final class Version20250112183000 extends AbstractMigration implements RepeatableMigrationInterface
{
    public function upgradeDatabase()
    {
        $this->refreshBlockType('page_list');
    }
}
