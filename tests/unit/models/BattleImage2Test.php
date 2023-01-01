<?php

declare(strict_types=1);

namespace tests\models;

use Codeception\Test\Unit;
use app\models\BattleImage2;

use function explode;
use function in_array;
use function preg_match;
use function substr;

class BattleImage2Test extends Unit
{
    public function testGenerateFilename(): void
    {
        $generated = [];
        for ($i = 0; $i < 5; ++$i) {
            $value = BattleImage2::generateFilename(false);
            $this->assertIsString($value);
            $this->assertFalse(in_array($value, $generated));
            $this->assertEquals(1, preg_match(
                '#^[a-z2-7]{2}/[a-z2-7]{26}\.jpg$#',
                $value,
            ));

            $parts = explode('/', $value);
            $this->assertEquals($parts[0], substr($parts[1], 0, 2));

            $generated[] = $value;
        }
    }
}
