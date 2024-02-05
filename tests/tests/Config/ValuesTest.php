<?php

namespace Concrete\Tests\Config;

use Concrete\Tests\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

class ValuesTest extends TestCase
{
    public static function provideConfiguredAliases()
    {
        $config = app('config');
        $result = [];
        foreach ($config->get('app.aliases') as $alias => $actualClass) {
            $result[] = ['app.aliases', $alias, $actualClass];
        }
        foreach ($config->get('app.facades') as $alias => $actualClass) {
            $result[] = ['app.facades', $alias, $actualClass];
        }

        return $result;
    }

    /**
     * @param string $configKey
     * @param string $alias
     * @param string $classFromConfig
     */
    #[DataProvider('provideConfiguredAliases')]
    public function testConfiguredAliases($configKey, $alias, $classFromConfig)
    {
        $alias = ltrim($alias, '\\');
        $classFromConfig = ltrim($classFromConfig, '\\');
        $this->assertTrue(class_exists($alias, true));
        $actualClass = (new \ReflectionClass($alias))->getName();
        $this->assertSame($classFromConfig, $actualClass, "Accordingly to {$configKey}, {$alias} should resolve to {$classFromConfig}, but it resolves to {$actualClass}");
    }
}
