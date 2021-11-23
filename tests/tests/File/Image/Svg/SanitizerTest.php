<?php

namespace Concrete\Tests\File\Image\Svg;

use Concrete\Core\File\Image\Svg\Sanitizer;
use Concrete\Core\File\Image\Svg\SanitizerOptions;
use Concrete\Core\Support\Facade\Application;
use Illuminate\Filesystem\Filesystem;
use Mockery;
use Concrete\Tests\TestCase;

class SanitizerTest extends TestCase
{
    /**
     * @var \Concrete\Core\File\Image\Svg\Sanitizer
     */
    protected static $sanitizer;

    /**
     * @var \Concrete\Core\File\Image\Svg\SanitizerOptions
     */
    protected static $sanitizerOptions;

    /**
     * {@inheritdoc}
     *
     * @see PHPUnit_Framework_TestCase::setupBeforeClass()
     */
    public static function setupBeforeClass():void
    {
        parent::setUpBeforeClass();
        $app = Application::getFacadeApplication();
        self::$sanitizer = $app->build(Sanitizer::class);
        self::$sanitizerOptions = new SanitizerOptions();
        self::$sanitizerOptions
            ->setUnsafeElements('script script2')
            ->setElementAllowlist('script2')
            ->setUnsafeAttributes('onload onload2 onclick')
            ->setAttributeAllowlist('onload2');
    }

    /**
     * {@inheritdoc}
     *
     * @see PHPUnit_Framework_TestCase::tearDown()
     */
    public function TearDown():void
    {
        Mockery::close();
    }

    /**
     * @return array
     */
    public function provideSanitizeWithDefaultSettings()
    {
        return [
            ['<svg/>', '<svg></svg>'],
            ['<svg good="1" />', '<svg></svg>'],
            ['<svg><script>alert(1);</script></svg>', '<svg></svg>'],
            ['<svg><script2>alert(1);</script2></svg>', '<svg></svg>'],
            ['<svg onload="alert(1)" />', '<svg></svg>'],
            ['<svg foo="1" onload="alert(1)" bar="2" />', '<svg></svg>'],
            ['<svg foo="1" OnLoad="alert(1)" OnLoad2="alert(1)" bar="2" />', '<svg></svg>'],
            ['<svg><script></script><g onLoad="alert(1)"><rect /></g></svg>', '<svg>  <g>    <rect></rect>  </g></svg>'],
        ];
    }

    /**
     * @param string $input
     * @param string $expectedOutput
     * @dataProvider provideSanitizeWithDefaultSettings
     */
    public function testSanitizeWithDefaultSettings($input, $expectedOutput)
    {
        $sanitized = self::$sanitizer->sanitizeData($input, self::$sanitizerOptions);
        $lines = explode("\n", $sanitized);
        static::assertRegExp('/^<\?xml\b[^>]*\?>$/', array_shift($lines));
        $xml = trim(implode('', $lines));

        static::assertSame($expectedOutput, $xml);
    }

    public function testLoadInvalidFile()
    {
        $this->expectException(\Concrete\Core\File\Image\Svg\SanitizerException::class);
        self::$sanitizer->sanitizeFile(__DIR__ . '/does-not-exist');
    }

    /**
     * @return array
     */
    public function provideInvalidData()
    {
        return [
            ['<svg'],
        ];
    }

    /**
     *
     *
     * @param mixed $invalidSvgData
     * @dataProvider provideInvalidData
     */
    public function testInvalidData($invalidSvgData)
    {
        $this->expectException(\Concrete\Core\File\Image\Svg\SanitizerException::class);
        self::$sanitizer->sanitizeData($invalidSvgData, self::$sanitizerOptions);
    }

    public function testShouldThrowIfFileDoesNotExist()
    {
        $this->expectException(\Concrete\Core\File\Image\Svg\SanitizerException::class);
        $filename = __DIR__ . '/test-file';
        $fs = Mockery::mock(Filesystem::class);
        $fs->shouldReceive('isFile')->once()->with($filename)->andReturn(false);
        $fs->shouldReceive('get')->never();
        $fs->shouldReceive('put')->never();
        $sanitizer = new Sanitizer($fs);
        $sanitizer->sanitizeFile($filename);
    }

    public function testShouldSaveIfNothingChangedButOtherFilename()
    {
        $filename = __DIR__ . '/test-file';
        $filename2 = __DIR__ . '/test-file-2';
        $fs = Mockery::mock(Filesystem::class);
        $fs->shouldReceive('isFile')->once()->with($filename)->andReturn(true);
        $fs->shouldReceive('get')->once()->with($filename)->andReturn("<?xml version=\"1.0\"?>\n<svg/>\n");
        $fs->shouldReceive('put')->once()->with($filename2, "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<svg></svg>\n");
        $sanitizer = new Sanitizer($fs);
        $sanitizer->sanitizeFile($filename, self::$sanitizerOptions, $filename2);
    }

}
