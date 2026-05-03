<?php

declare(strict_types=1);

namespace tests\helpers;

use Codeception\Test\Unit;
use app\components\helpers\DataUri;

use function base64_encode;

class DataUriTest extends Unit
{
    public function testParsePngBase64(): void
    {
        $binary = "\x89PNG\r\n\x1a\n";
        $uri = 'data:image/png;base64,' . base64_encode($binary);
        $this->assertSame(['image/png', $binary], DataUri::parse($uri));
    }

    public function testParseSvgBase64WithPlusInSubtype(): void
    {
        $binary = '<svg xmlns="http://www.w3.org/2000/svg"></svg>';
        $uri = 'data:image/svg+xml;base64,' . base64_encode($binary);
        $this->assertSame(['image/svg+xml', $binary], DataUri::parse($uri));
    }

    public function testParseLowercasesMimeType(): void
    {
        $uri = 'data:Image/PNG;base64,' . base64_encode('x');
        $result = DataUri::parse($uri);
        $this->assertNotNull($result);
        $this->assertSame('image/png', $result[0]);
    }

    public function testParseEmptyStringReturnsNull(): void
    {
        $this->assertNull(DataUri::parse(''));
    }

    public function testParseNonDataUriReturnsNull(): void
    {
        $this->assertNull(DataUri::parse('https://example.com/icon.png'));
    }

    public function testParseRejectsNonBase64Encoding(): void
    {
        // Plain (URL-encoded) data URI is unsupported by this parser.
        $this->assertNull(DataUri::parse('data:text/plain,Hello%20World'));
    }

    public function testParseRejectsCorruptBase64(): void
    {
        $this->assertNull(DataUri::parse('data:image/png;base64,!!!not-base64!!!'));
    }
}
