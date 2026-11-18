<?php

declare(strict_types=1);

namespace tests\helpers;

use Codeception\Test\Unit;
use app\components\helpers\TypicalGearConfiguration3;

use function array_map;

class TypicalGearConfiguration3Test extends Unit
{
    /**
     * Abilities in rank order. Value is the gear type for primary-only abilities.
     */
    private const array ABILITIES = [
        'ink_saver_main' => null,
        'ink_saver_sub' => null,
        'ink_recovery_up' => null,
        'run_speed_up' => null,
        'swim_speed_up' => null,
        'special_charge_up' => null,
        'special_saver' => null,
        'special_power_up' => null,
        'quick_respawn' => null,
        'quick_super_jump' => null,
        'sub_power_up' => null,
        'ink_resistance_up' => null,
        'sub_resistance_up' => null,
        'intensify_action' => null,
        'opening_gambit' => 'headgear',
        'last_ditch_effort' => 'headgear',
        'tenacity' => 'headgear',
        'comeback' => 'headgear',
        'ninja_squid' => 'clothing',
        'haunt' => 'clothing',
        'thermal_ink' => 'clothing',
        'respawn_punisher' => 'clothing',
        'ability_doubler' => 'clothing',
        'stealth_jump' => 'shoes',
        'object_shredder' => 'shoes',
        'drop_roller' => 'shoes',
    ];

    public function testBuildsTypicalConfiguration(): void
    {
        // stealth_jump (70%) is adopted, comeback (40%) and drop_roller (10%) are not.
        // swim_speed_up and ink_saver_main get the free primaries, and their extra GP
        // goes to the secondaries of the same gear.
        // Secondary-only abilities with 2 slots are not split across gears.
        $this->assertSame(
            [
                'headgear' => [
                    'primary' => 'swim_speed_up',
                    'secondaries' => ['swim_speed_up', 'swim_speed_up', 'special_charge_up'],
                ],
                'clothing' => [
                    'primary' => 'ink_saver_main',
                    'secondaries' => ['ink_saver_main', 'quick_super_jump', 'quick_super_jump'],
                ],
                'shoes' => [
                    'primary' => 'stealth_jump',
                    'secondaries' => ['ink_resistance_up', 'ink_resistance_up', 'sub_resistance_up'],
                ],
            ],
            TypicalGearConfiguration3::build(self::ABILITIES, $this->getBasicAverages()),
        );
    }

    public function testRegularAveragesAreNormalized(): void
    {
        $averages = $this->getBasicAverages();
        foreach ($averages as $key => $value) {
            if (self::ABILITIES[$key] === null) {
                $averages[$key] = $value * 0.5;
            }
        }

        $this->assertSame(
            TypicalGearConfiguration3::build(self::ABILITIES, $this->getBasicAverages()),
            TypicalGearConfiguration3::build(self::ABILITIES, $averages),
        );
    }

    public function testSecondariesOverflowFromPrimaryGear(): void
    {
        // swim_speed_up (22 GP) = 1 primary + 4 secondaries. The 4th secondary overflows
        // to another gear. ink_resistance_up (9 GP) = 3 secondaries on one gear.
        $this->assertSame(
            [
                'headgear' => [
                    'primary' => 'swim_speed_up',
                    'secondaries' => ['swim_speed_up', 'swim_speed_up', 'swim_speed_up'],
                ],
                'clothing' => [
                    'primary' => 'ink_saver_main',
                    'secondaries' => ['ink_resistance_up', 'ink_resistance_up', 'ink_resistance_up'],
                ],
                'shoes' => [
                    'primary' => 'ink_recovery_up',
                    'secondaries' => ['swim_speed_up', 'quick_super_jump', 'quick_super_jump'],
                ],
            ],
            TypicalGearConfiguration3::build(self::ABILITIES, [
                'swim_speed_up' => 22.0,
                'ink_saver_main' => 10.0,
                'ink_recovery_up' => 10.0,
                'ink_resistance_up' => 9.0,
                'quick_super_jump' => 6.0,
            ]),
        );
    }

    public function testPrimaryOnlyAbilitySelection(): void
    {
        $averages = [
            'comeback' => 3.0,
            'last_ditch_effort' => 6.0,
            'swim_speed_up' => 30.0,
            'ink_saver_main' => 27.0,
        ];

        // The most mounted one in the gear type is adopted.
        $this->assertSame(
            'last_ditch_effort',
            $this->getPrimaries(TypicalGearConfiguration3::build(self::ABILITIES, $averages))['headgear'],
        );

        // Not adopted when its rate is lower than the threshold.
        $this->assertNotContains(
            'last_ditch_effort',
            $this->getPrimaries(TypicalGearConfiguration3::build(self::ABILITIES, $averages, 0.7)),
        );

        // The threshold itself is inclusive.
        $this->assertSame(
            'last_ditch_effort',
            $this->getPrimaries(TypicalGearConfiguration3::build(self::ABILITIES, $averages, 0.6))['headgear'],
        );
    }

    public function testReturnsNullWithoutData(): void
    {
        $this->assertNull(TypicalGearConfiguration3::build(self::ABILITIES, []));
        $this->assertNull(TypicalGearConfiguration3::build(self::ABILITIES, ['swim_speed_up' => 0.0]));

        // Primary-only abilities alone cannot fill the configuration.
        $this->assertNull(TypicalGearConfiguration3::build(self::ABILITIES, ['stealth_jump' => 10.0]));
    }

    /**
     * @return array<string, float>
     */
    private function getBasicAverages(): array
    {
        return [
            'stealth_jump' => 7.0,
            'comeback' => 4.0,
            'drop_roller' => 1.0,
            'swim_speed_up' => 16.0,
            'ink_saver_main' => 13.0,
            'quick_super_jump' => 6.0,
            'ink_resistance_up' => 6.0,
            'sub_resistance_up' => 3.0,
            'special_charge_up' => 3.0,
        ];
    }

    /**
     * @param array<string, array{primary: string, secondaries: list<string>}>|null $config
     * @return array<string, string>
     */
    private function getPrimaries(?array $config): array
    {
        $this->assertNotNull($config);

        return array_map(fn (array $gear): string => $gear['primary'], $config);
    }
}
