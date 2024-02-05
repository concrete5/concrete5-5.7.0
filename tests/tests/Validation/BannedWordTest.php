<?php

namespace Concrete\Tests\Validation;

use Concrete\Core\Validation\BannedWord\Service;
use Concrete\Tests\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

class BannedWordTest extends TestCase
{
    protected static $asciiSingleParagraphString = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.';
    protected static $asciiMultipleParagraphString = <<<EOT
Lorem ipsum dolor sit amet, consectetur adipiscing elit.
Quis vel eros "donec" ac odio tempor orci dapibus.

Tortor id aliquet lectus proin. Faucibus turpis in eu mi bibendum neque.
    A pellentesque sit amet porttitor eget dolor morbi non arcu.
EOT;
    protected static $multibyteSingleParagraphString = 'Duis aute irure dolör in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla päriatur.';
    protected static $cjkMultipleParagraphString = <<<EOT
吾輩は猫である。名前はまだ無い。
どこで生れたかとんと見当がつかぬ。

봄날의 소금이라 것은 위하여, 인간의 능히 있는 것이다. 

裡下候買想期；道驚非，過同病，風成男她；些通神草重，得辦已型車中可？
密能生式民的界度引……華課此傳上先，長同的數改好、命你書夫業角開人。
EOT;

    /**
     * @param string $bannedWord
     * @param string $string
     */
    #[DataProvider('matchBannedWordsProvider')]
    public function testHasBannedWord(string $bannedWord, string $string): void
    {
        $service = new Service();
        self::assertTrue($service->hasBannedWord($bannedWord, $string));
    }

    public static function matchBannedWordsProvider(): array
    {
        return [
            ['lorem', self::$asciiSingleParagraphString], // Ascii word, case insensitive
            ['donec', self::$asciiMultipleParagraphString], // Word wrapped by quotes
            ['dolör', self::$multibyteSingleParagraphString], // Word contains umlauts
            ['名前はまだ無い', self::$cjkMultipleParagraphString], // Agglutinative language word
            ['*esque', self::$asciiMultipleParagraphString], // Wildcard at the beginning of the word
            ['päria*', self::$multibyteSingleParagraphString], // Wildcard at the end of the word
            ['裡下*想期', self::$cjkMultipleParagraphString], // Wildcard at the middle of the word
            ['*吾輩*', self::$cjkMultipleParagraphString], // Word wrapped by wildcards
        ];
    }

    /**
     * @param string $bannedWord
     * @param string $string
     */
    #[DataProvider('doesNotMatchBannedWordsProvider')]
    public function testHasNotBannedWord(string $bannedWord, string $string): void
    {
        $service = new Service();
        self::assertNotTrue($service->hasBannedWord($bannedWord, $string));
    }

    public static function doesNotMatchBannedWordsProvider(): array
    {
        return [
            ['ore', self::$asciiSingleParagraphString], // The part of the word without wildcard
            ['lectusproin', self::$asciiMultipleParagraphString], // Combined words
            ['봄날의*위하여', self::$cjkMultipleParagraphString], // Wildcard should not match whitespaces
        ];
    }

    /**
     * @param string $bannedWord
     * @param string $string
     */
    #[DataProvider('invalidBannedWordsProvider')]
    public function testEscapeBannedWord(string $bannedWord, string $string): void
    {
        $service = new Service();
        self::assertNotTrue($service->hasBannedWord($bannedWord, $string));
    }

    public static function invalidBannedWordsProvider(): array
    {
        return [
            ['C[a-z]+', 'Concrete CMS'],
            ['.+', 'Concrete CMS'],
        ];
    }
}