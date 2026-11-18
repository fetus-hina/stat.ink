<?php

/**
 * @copyright Copyright (C) 2015-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

namespace app\components\helpers;

use Generator;
use LogicException;

use function array_diff;
use function array_filter;
use function array_keys;
use function array_map;
use function array_merge;
use function array_sum;
use function array_values;
use function count;
use function max;
use function min;
use function usort;

/**
 * Builds a typical gear configuration of Splatoon 3 from the average GPs.
 *
 * The averages are the values of stat_ability3_x_usage, that is, the primary ability is
 * counted as 10 and the secondary is counted as 3.
 *
 * Note: The doubled secondaries by Ability Doubler are not taken into account.
 *
 * @phpstan-type TypicalGear3 array{primary: string, secondaries: list<string>}
 * @phpstan-type TypicalGearConfig3 array{headgear: TypicalGear3, clothing: TypicalGear3, shoes: TypicalGear3}
 */
final class TypicalGearConfiguration3
{
    public const string HEADGEAR = 'headgear';
    public const string CLOTHING = 'clothing';
    public const string SHOES = 'shoes';

    public const array GEAR_TYPES = [
        self::HEADGEAR,
        self::CLOTHING,
        self::SHOES,
    ];

    private const int PRIMARY_GP = 10;
    private const int SECONDARY_GP = 3;
    private const int SECONDARY_SLOTS = 3;
    private const float EPSILON = 1.0e-9;

    /**
     * @param array<string, string|null> $abilities Ability keys in rank order.
     *   The value is the gear type key for the primary-only abilities, null otherwise.
     * @param array<string, float> $averages Ability key => average GP
     * @param float $primaryOnlyThreshold Minimum mounting rate to adopt a primary-only ability
     * @return TypicalGearConfig3|null
     */
    public static function build(
        array $abilities,
        array $averages,
        float $primaryOnlyThreshold = 0.5,
    ): ?array {
        $primaries = self::selectPrimaryOnlyAbilities($abilities, $averages, $primaryOnlyThreshold);

        $regulars = [];
        foreach ($abilities as $key => $gearType) {
            if ($gearType === null) {
                $regulars[$key] = max(0.0, $averages[$key] ?? 0.0);
            }
        }

        $total = array_sum($regulars);
        if ($total <= self::EPSILON) {
            return null;
        }

        // Normalize the averages to the GPs that the regular abilities can actually take
        $freeGearTypes = array_values(array_diff(self::GEAR_TYPES, array_keys($primaries)));
        $secondarySlots = self::SECONDARY_SLOTS * count(self::GEAR_TYPES);
        $budget = self::PRIMARY_GP * count($freeGearTypes) + self::SECONDARY_GP * $secondarySlots;
        $targets = array_map(fn (float $v): float => $v * $budget / $total, $regulars);

        [$primaryKeys, $secondaryCounts] = self::allocate($targets, count($freeGearTypes), $secondarySlots);
        foreach ($freeGearTypes as $i => $gearType) {
            $primaries[$gearType] = $primaryKeys[$i];
        }

        $orderedPrimaries = [];
        foreach (self::GEAR_TYPES as $gearType) {
            $orderedPrimaries[$gearType] = $primaries[$gearType] ?? throw new LogicException();
        }

        $secondaries = self::placeSecondaries(
            $orderedPrimaries,
            $secondaryCounts,
            array_keys($abilities),
        );

        return [
            self::HEADGEAR => [
                'primary' => $orderedPrimaries[self::HEADGEAR],
                'secondaries' => $secondaries[self::HEADGEAR],
            ],
            self::CLOTHING => [
                'primary' => $orderedPrimaries[self::CLOTHING],
                'secondaries' => $secondaries[self::CLOTHING],
            ],
            self::SHOES => [
                'primary' => $orderedPrimaries[self::SHOES],
                'secondaries' => $secondaries[self::SHOES],
            ],
        ];
    }

    /**
     * Adopts the most mounted primary-only ability for each gear type
     * if its mounting rate reaches the threshold.
     *
     * @param array<string, string|null> $abilities
     * @param array<string, float> $averages
     * @return array<string, string> Gear type => ability key
     */
    private static function selectPrimaryOnlyAbilities(
        array $abilities,
        array $averages,
        float $threshold,
    ): array {
        $results = [];
        $bestRates = [];
        foreach ($abilities as $key => $gearType) {
            if ($gearType === null) {
                continue;
            }

            $rate = ($averages[$key] ?? 0.0) / self::PRIMARY_GP;
            if ($rate + self::EPSILON < $threshold) {
                continue;
            }

            if (!isset($bestRates[$gearType]) || $rate > $bestRates[$gearType] + self::EPSILON) {
                $results[$gearType] = $key;
                $bestRates[$gearType] = $rate;
            }
        }

        return $results;
    }

    /**
     * Allocates the primaries and the secondaries to minimize the squared error from the targets.
     *
     * All combinations of the primaries are tried. For a fixed set of primaries, allocating the
     * secondaries greedily to the largest residual is optimal.
     *
     * @param array<string, float> $targets Ability key => target GP, in rank order
     * @return array{list<string>, array<string, int>} Primary keys in descending order of
     *   the target, and the numbers of the secondaries in rank order
     */
    private static function allocate(array $targets, int $primarySlots, int $secondarySlots): array
    {
        $keys = array_keys($targets);
        $bestError = null;
        $bestPrimaries = [];
        $bestSecondaries = [];
        foreach (self::combinations(count($keys), $primarySlots) as $indices) {
            $primaries = array_map(fn (int $i): string => $keys[$i], $indices);

            $residuals = $targets;
            foreach ($primaries as $key) {
                $residuals[$key] -= self::PRIMARY_GP;
            }

            $secondaries = array_map(fn (): int => 0, $targets);
            for ($n = 0; $n < $secondarySlots; ++$n) {
                $selected = null;
                foreach ($residuals as $key => $value) {
                    if ($selected === null || $value > $residuals[$selected] + self::EPSILON) {
                        $selected = $key;
                    }
                }

                if ($selected === null) {
                    throw new LogicException();
                }

                $residuals[$selected] -= self::SECONDARY_GP;
                ++$secondaries[$selected];
            }

            $error = array_sum(array_map(fn (float $v): float => $v * $v, $residuals));
            if ($bestError === null || $error < $bestError - self::EPSILON) {
                $bestError = $error;
                $bestPrimaries = $primaries;
                $bestSecondaries = $secondaries;
            }
        }

        // Stable sort: ties keep the rank order
        usort($bestPrimaries, fn (string $a, string $b): int => $targets[$b] <=> $targets[$a]);

        return [$bestPrimaries, $bestSecondaries];
    }

    /**
     * Yields non-decreasing index lists (combinations with repetition).
     *
     * @return Generator<list<int>>
     */
    private static function combinations(int $n, int $k, int $start = 0): Generator
    {
        if ($k === 0) {
            yield [];
            return;
        }

        for ($i = $start; $i < $n; ++$i) {
            foreach (self::combinations($n, $k - 1, $i) as $rest) {
                yield array_merge([$i], $rest);
            }
        }
    }

    /**
     * Places the secondaries keeping the same abilities together as much as possible.
     *
     * 1. The secondaries of the same ability as the primary go to that gear first.
     * 2. The rest are placed in descending order of the number, to the gear with
     *    the least free slots that can take all of them (best-fit decreasing).
     *    If no gear can take them all, they are split, starting with the gear with
     *    the most free slots.
     *
     * @param array<string, string> $primaries Gear type => ability key
     * @param array<string, int> $counts Ability key => number of the secondaries, in rank order
     * @param list<string> $rankOrder
     * @return array<string, list<string>> Gear type => secondary ability keys
     */
    private static function placeSecondaries(array $primaries, array $counts, array $rankOrder): array
    {
        $free = array_map(fn (): int => self::SECONDARY_SLOTS, $primaries);
        $placed = array_map(fn (): array => [], $primaries);

        foreach ($primaries as $gearType => $key) {
            $n = min($counts[$key] ?? 0, $free[$gearType]);
            if ($n > 0) {
                $placed[$gearType][$key] = $n;
                $free[$gearType] -= $n;
                $counts[$key] -= $n;
            }
        }

        $items = array_filter($counts, fn (int $n): bool => $n > 0);
        $itemKeys = array_keys($items);
        usort($itemKeys, fn (string $a, string $b): int => $items[$b] <=> $items[$a]);

        foreach ($itemKeys as $key) {
            $rest = $items[$key];
            while ($rest > 0) {
                $gearType = self::findGearForSecondaries($free, $rest);
                $n = min($rest, $free[$gearType]);
                $placed[$gearType][$key] = ($placed[$gearType][$key] ?? 0) + $n;
                $free[$gearType] -= $n;
                $rest -= $n;
            }
        }

        $results = [];
        foreach ($primaries as $gearType => $primaryKey) {
            $list = [];
            $orderedKeys = array_merge(
                [$primaryKey],
                array_values(array_filter($rankOrder, fn (string $k): bool => $k !== $primaryKey)),
            );
            foreach ($orderedKeys as $key) {
                for ($i = 0; $i < ($placed[$gearType][$key] ?? 0); ++$i) {
                    $list[] = $key;
                }
            }
            $results[$gearType] = $list;
        }

        return $results;
    }

    /**
     * @param array<string, int> $free Gear type => number of the free slots
     */
    private static function findGearForSecondaries(array $free, int $count): string
    {
        $fit = null;
        $largest = null;
        foreach ($free as $gearType => $n) {
            if ($n >= $count && ($fit === null || $n < $free[$fit])) {
                $fit = $gearType;
            }

            if ($n > 0 && ($largest === null || $n > $free[$largest])) {
                $largest = $gearType;
            }
        }

        return $fit ?? $largest ?? throw new LogicException('No free slots');
    }
}
