<?php

declare(strict_types=1);

namespace tests\helpers;

use Codeception\Test\Unit;
use app\components\helpers\SendouInk;
use app\models\Weapon3;
use app\models\Weapon3Alias;

use function array_map;

class SendouInkTest extends Unit
{
    private const array CODES = [
        'ink_saver_main' => 'ISM',
        'swim_speed_up' => 'SSU',
        'quick_super_jump' => 'QSJ',
        'ink_resistance_up' => 'RES',
        'comeback' => 'CB',
        'stealth_jump' => 'SJ',
    ];

    public function testBuildUrlFromConfig(): void
    {
        $this->assertSame(
            'https://sendou.ink/analyzer?weapon=40&build=CB%2CSSU%2CSSU%2CQSJ%2CISM%2CISM%2CISM%2CRES%2CSJ%2CQSJ%2CRES%2CRES',
            SendouInk::getBuildUrl3FromConfig(
                $this->createWeapon(['splattershot', '40']),
                [
                    'headgear' => [
                        'primary' => 'comeback',
                        'secondaries' => ['swim_speed_up', 'swim_speed_up', 'quick_super_jump'],
                    ],
                    'clothing' => [
                        'primary' => 'ink_saver_main',
                        'secondaries' => ['ink_saver_main', 'ink_saver_main', 'ink_resistance_up'],
                    ],
                    'shoes' => [
                        'primary' => 'stealth_jump',
                        'secondaries' => ['quick_super_jump', 'ink_resistance_up', 'ink_resistance_up'],
                    ],
                ],
                self::CODES,
            ),
        );
    }

    public function testUnknownAbilitiesAndMissingSecondaries(): void
    {
        // Weapon ID 0 is valid (Sploosh-o-matic)
        $this->assertSame(
            'https://sendou.ink/analyzer?weapon=0&build=SSU%2CU%2CU%2CU%2CISM%2CISM%2CU%2CU%2CSJ%2CU%2CU%2CU',
            SendouInk::getBuildUrl3FromConfig(
                $this->createWeapon(['0']),
                [
                    'headgear' => ['primary' => 'swim_speed_up', 'secondaries' => []],
                    'clothing' => ['primary' => 'ink_saver_main', 'secondaries' => ['ink_saver_main']],
                    'shoes' => ['primary' => 'stealth_jump', 'secondaries' => ['unknown_ability']],
                ],
                self::CODES,
            ),
        );
    }

    public function testReturnsNullWithoutWeaponId(): void
    {
        $config = [
            'headgear' => ['primary' => 'swim_speed_up', 'secondaries' => []],
            'clothing' => ['primary' => 'swim_speed_up', 'secondaries' => []],
            'shoes' => ['primary' => 'swim_speed_up', 'secondaries' => []],
        ];

        $this->assertNull(SendouInk::getBuildUrl3FromConfig(null, $config, self::CODES));
        $this->assertNull(
            SendouInk::getBuildUrl3FromConfig($this->createWeapon(['splattershot']), $config, self::CODES),
        );
    }

    /**
     * @param string[] $aliases
     */
    private function createWeapon(array $aliases): Weapon3
    {
        $weapon = new Weapon3();
        $weapon->populateRelation(
            'weapon3Aliases',
            array_map(fn (string $key): Weapon3Alias => new Weapon3Alias(['key' => $key]), $aliases),
        );

        return $weapon;
    }
}
