<?php

namespace Concrete\TestHelpers\Config\Fixtures;

use Concrete\Core\Config\FileSaver;

class TestFileSaver extends FileSaver
{
    protected function getStorageDirectory()
    {
        return DIR_TESTS . '/config/generated_overrides';
    }
}
