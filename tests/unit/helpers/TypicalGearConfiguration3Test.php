<?php

declare(strict_types=1);

namespace tests\helpers;

use Codeception\Test\Unit;
use app\components\helpers\TypicalGearConfiguration3;

use function array_column;
use function array_map;
use function array_unique;
use function count;
use function json_encode;

use const SORT_REGULAR;

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

    public function testBestCandidate(): void
    {
        // stealth_jump (70%) is adopted, comeback (40%) and drop_roller (10%) are not.
        // swim_speed_up and ink_saver_main get the free primaries, and their extra GP
        // goes to the secondaries of the same gear.
        // Secondary-only abilities with 2 slots are not split across gears.
        //
        // error = (7 - 10)^2 [stealth_jump] + 4^2 [comeback] + 1^2 [drop_roller] = 26
        // (the regular abilities fit exactly)
        $candidates = TypicalGearConfiguration3::build(self::ABILITIES, $this->getBasicAverages());
        $this->assertSame(
            [
                'error' => 26.0,
                'config' => [
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
            ],
            $candidates[0] ?? null,
        );
    }

    public function testCandidatesAreRankedAndDistinct(): void
    {
        $candidates = TypicalGearConfiguration3::build(self::ABILITIES, $this->getBasicAverages());
        $this->assertCount(3, $candidates);

        $errors = array_column($candidates, 'error');
        $this->assertLessThan($errors[1], $errors[0]);
        $this->assertLessThanOrEqual($errors[2], $errors[1]);

        // Candidates differ in the primaries, not only in the secondaries
        $primaries = array_map(
            fn (array $candidate): string => (string)json_encode($this->getPrimaries($candidate)),
            $candidates,
        );
        $this->assertCount(3, array_unique($primaries, SORT_REGULAR));
    }

    public function testLimit(): void
    {
        $this->assertCount(1, TypicalGearConfiguration3::build(self::ABILITIES, $this->getBasicAverages(), 1));
        $this->assertCount(5, TypicalGearConfiguration3::build(self::ABILITIES, $this->getBasicAverages(), 5));
    }

    public function testSecondariesOverflowFromPrimaryGear(): void
    {
        // swim_speed_up (22 GP) = 1 primary + 4 secondaries. The 4th secondary overflows
        // to another gear. ink_resistance_up (9 GP) = 3 secondaries on one gear.
        $candidates = TypicalGearConfiguration3::build(self::ABILITIES, [
            'swim_speed_up' => 22.0,
            'ink_saver_main' => 10.0,
            'ink_recovery_up' => 10.0,
            'ink_resistance_up' => 9.0,
            'quick_super_jump' => 6.0,
        ]);
        $this->assertSame(
            [
                'error' => 0.0,
                'config' => [
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
            ],
            $candidates[0] ?? null,
        );
    }

    public function testSecondariesAreOrderedByTotalGp(): void
    {
        // quick_super_jump (22 GP) = 1 primary + 4 secondaries. The 4th secondary overflows to
        // the shoes, and is placed before ink_recovery_up (6 GP) which precedes it in rank order.
        $candidates = TypicalGearConfiguration3::build(self::ABILITIES, [
            'quick_super_jump' => 22.0,
            'ink_saver_main' => 10.0,
            'swim_speed_up' => 10.0,
            'ink_resistance_up' => 9.0,
            'ink_recovery_up' => 6.0,
        ]);
        $this->assertSame(
            [
                'headgear' => [
                    'primary' => 'quick_super_jump',
                    'secondaries' => ['quick_super_jump', 'quick_super_jump', 'quick_super_jump'],
                ],
                'clothing' => [
                    'primary' => 'ink_saver_main',
                    'secondaries' => ['ink_resistance_up', 'ink_resistance_up', 'ink_resistance_up'],
                ],
                'shoes' => [
                    'primary' => 'swim_speed_up',
                    'secondaries' => ['quick_super_jump', 'ink_recovery_up', 'ink_recovery_up'],
                ],
            ],
            ($candidates[0] ?? null)['config'] ?? null,
        );
    }

    public function testPrimaryOnlyAbilityAdoption(): void
    {
        // The most mounted one in the gear type is adopted
        $candidates = TypicalGearConfiguration3::build(self::ABILITIES, [
            'comeback' => 3.0,
            'last_ditch_effort' => 6.0,
            'swim_speed_up' => 21.0,
            'ink_saver_main' => 27.0,
        ]);
        $this->assertSame('last_ditch_effort', $this->getPrimaries($candidates[0] ?? null)['headgear']);

        // Rarely mounted one is not adopted
        $candidates = TypicalGearConfiguration3::build(self::ABILITIES, [
            'stealth_jump' => 2.0,
            'swim_speed_up' => 16.0,
            'ink_saver_main' => 13.0,
            'ink_recovery_up' => 8.0,
            'quick_super_jump' => 6.0,
            'ink_resistance_up' => 6.0,
            'sub_resistance_up' => 3.0,
            'special_charge_up' => 3.0,
        ]);
        $this->assertNotContains('stealth_jump', $this->getPrimaries($candidates[0] ?? null));
    }

    public function testPrimaryOnlyMinRate(): void
    {
        // With a large limit, all the candidates are returned.
        // 14 regular abilities: 3 primaries = C(16, 3) = 560 combinations, 2 primaries = C(15, 2) = 105.
        $averages = [
            'stealth_jump' => 2.0,
            'swim_speed_up' => 55.0,
        ];

        // 20% (default threshold, inclusive): with or without stealth_jump
        $candidates = TypicalGearConfiguration3::build(self::ABILITIES, $averages, 1000);
        $this->assertCount(560 + 105, $candidates);

        // Not a candidate when the rate is lower than the threshold
        $candidates = TypicalGearConfiguration3::build(self::ABILITIES, $averages, 1000, 0.3);
        $this->assertCount(560, $candidates);
        foreach ($candidates as $candidate) {
            $this->assertNotContains('stealth_jump', $this->getPrimaries($candidate));
        }
    }

    public function testReturnsEmptyWithoutData(): void
    {
        $this->assertSame([], TypicalGearConfiguration3::build(self::ABILITIES, []));
        $this->assertSame([], TypicalGearConfiguration3::build(self::ABILITIES, ['swim_speed_up' => 0.0]));

        // Primary-only abilities alone cannot fill the configuration
        $this->assertSame([], TypicalGearConfiguration3::build(self::ABILITIES, ['stealth_jump' => 10.0]));
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
     * @param array{error: float, config: array<string, array{primary: string, secondaries: list<string>}>}|null $candidate
     * @return array<string, string>
     */
    private function getPrimaries(?array $candidate): array
    {
        $this->assertNotNull($candidate);

        return array_map(fn (array $gear): string => $gear['primary'], $candidate['config']);
    }
}
