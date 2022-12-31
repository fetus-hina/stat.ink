<?php

declare(strict_types=1);

namespace tests\helpers;

use Codeception\Test\Unit;
use app\components\helpers\RandomFilename;
use app\components\helpers\randomFilename\Generator;

use function basename;
use function bin2hex;
use function hex2bin;
use function in_array;
use function preg_match;
use function strlen;
use function substr;

class RandomFilenameTest extends Unit
{
    public function testGenerateUuidV4Binary(): void
    {
        for ($i = 0; $i < 10; ++$i) {
            $binary = Generator::generateUUIDv4Binary();
            $this->assertIsString($binary);
            $this->assertEquals(16, strlen($binary));

            $hex = bin2hex($binary);

            // check version
            $this->assertEquals('4', substr($hex, 12, 1));

            // check variant
            $this->assertTrue(in_array(substr($hex, 16, 1), ['8', '9', 'a', 'b', 'A', 'B']));
        }
    }

    /**
     * @dataProvider getFileNameDataset
     */
    public function testFormatFileNameFlat(
        string $vector,
        string $ext,
        int $unusedLevel,
        string $expect
    ): void {
        $this->assertEquals(
            basename($expect), // make flat
            Generator::formatFileNameFlat(
                hex2bin($vector),
                $ext,
            ),
        );
    }

    /**
     * @dataProvider getFileNameDataset
     */
    public function testFormatFileName(
        string $vector,
        string $ext,
        int $level,
        string $expect
    ): void {
        $this->assertEquals(
            $expect,
            Generator::formatFileName(hex2bin($vector), $ext, $level),
        );
    }

    public function getFileNameDataset(): array
    {
        return [
            ['7c3b90f406bc5f0bd5c7ee8ddf73757f', '', 0, 'pq5zb5agxrpqxvoh52g5643vp4'],
            ['e3ddde2a2a9b08deedc3e52f1b603739', 'jpg', 0, '4po54krktmen53od4uxrwybxhe.jpg'],
            ['10a40d281adfa68304ae3c432204804e', 'JPEG', 0, 'ccsa2ka236tigbfohrbsebeajy.JPEG'],
            ['7dbfb6ff8024dddc995f578cf296b4b0', 'png', 0, 'pw73n74aeto5zgk7k6gpffvuwa.png'],
            ['e439e23b86e7de891ca313f06b387bd1', '', 1, '4q/4q46eo4g47pishfdcpygwod32e'],
            ['856caa4319ddeaaa7f740966c369d90a', 'jpg', 1, 'qv/qvwkuqyz3xvku73ubftmg2ozbi.jpg'],
            ['affae0b6a9e720a3409abd3217991739', 'jpg', 2, 'v7/5o/v75obnvj44qkgqe2xuzbpgixhe.jpg'],
            ['f9faf8d3115c502230dd73b527444430', 'jpg', 3, '7h/5p/ru/7h5pruyrlricemg5oo2sorcega.jpg'],
            ['15f426686add674331d6612942573505', 'jpg', 4, 'cx/2c/m2/dk/cx2cm2dk3vtugmowmeuuevzvau.jpg'],
            ['7f71c4ffe23c0ac61dec18948e986a06', 'jpg', 5, 'p5/y4/j7/7c/hq/p5y4j77chqfmmhpmdcki5gdkay.jpg'],
        ];
    }

    public function testPublicInterface(): void
    {
        for ($i = 0; $i < 5; ++$i) {
            $value = RandomFilename::generate('jpg', 2);
            $this->assertIsString($value);

            $this->assertEquals(1, preg_match(
                '#^[a-z2-7]{2}/[a-z2-7]{2}/[a-z2-7]{26}\.jpg$#',
                $value,
            ));

            $parts = explode('/', $value);
            $this->assertEquals($parts[0], substr($parts[2], 0, 2));
            $this->assertEquals($parts[1], substr($parts[2], 2, 2));
        }
    }
}
