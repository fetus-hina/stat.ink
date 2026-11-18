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
use function array_pop;
use function array_splice;
use function array_sum;
use function array_values;
use function count;
use function max;
use function min;
use function usort;

/**
 * Builds typical gear configurations of Splatoon 3 from the average GPs.
 *
 * The averages are the values of stat_ability3_x_usage, that is, the primary ability is
 * counted as 10 and the secondary is counted as 3.
 *
 * The configurations are ranked by the squared error between their GPs and the averages.
 * The primary-only abilities are also counted, as 10 if mounted and 0 otherwise, so whether
 * to mount them is decided in the same way as the other abilities. Since the averages sum up
 * to 57 GP (3 primaries and 9 secondaries) in total, they are used as they are.
 *
 * The candidates differ in the primaries. For each set of the primaries, only the best
 * secondaries are taken.
 *
 * Note: The doubled secondaries by Ability Doubler are not taken into account.
 *
 * @phpstan-type TypicalGear3 array{primary: string, secondaries: list<string>}
 * @phpstan-type TypicalGearConfig3 array{headgear: TypicalGear3, clothing: TypicalGear3, shoes: TypicalGear3}
 * @phpstan-type TypicalGearCandidate3 array{error: float, config: TypicalGearConfig3}
 */
final class TypicalGearConfiguration3
{
    /**
     * Version of the calculation. Increment this when the results change for the same inputs,
     * so that the cached results are invalidated.
     */
    public const int VERSION = 2;

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
     * @param int $limit Maximum number of the candidates
     * @param float $primaryOnlyMinRate Minimum mounting rate for a primary-only ability to be
     *   considered (to limit the search space)
     * @return list<TypicalGearCandidate3> Candidates in ascending order of the error
     */
    public static function build(
        array $abilities,
        array $averages,
        int $limit = 3,
        float $primaryOnlyMinRate = 0.2,
    ): array {
        $regulars = [];
        $primaryOnlys = [];
        foreach ($abilities as $key => $gearType) {
            $average = max(0.0, (float)($averages[$key] ?? 0.0));
            if ($gearType === null) {
                $regulars[$key] = $average;
            } else {
                $primaryOnlys[$key] = ['gearType' => $gearType, 'average' => $average];
            }
        }

        if ($limit < 1 || array_sum($regulars) <= self::EPSILON) {
            return [];
        }

        // The error of the primary-only abilities when none of them are mounted
        $baseError = array_sum(
            array_map(fn (array $info): float => $info['average'] ** 2, $primaryOnlys),
        );

        $regularKeys = array_keys($regulars);
        $best = [];
        foreach (self::primaryOnlyPatterns($primaryOnlys, $primaryOnlyMinRate) as $pattern) {
            $patternError = $baseError;
            foreach ($pattern as $key) {
                $average = $primaryOnlys[$key]['average'];
                $patternError += ($average - self::PRIMARY_GP) ** 2 - $average ** 2;
            }

            // The pattern error is a lower bound of the total error
            if (!self::isCandidateAcceptable($best, $limit, $patternError)) {
                continue;
            }

            $freeGearTypes = array_values(array_diff(self::GEAR_TYPES, array_keys($pattern)));
            foreach (self::combinations(count($regularKeys), count($freeGearTypes)) as $indices) {
                $residuals = $regulars;
                foreach ($indices as $i) {
                    $residuals[$regularKeys[$i]] -= self::PRIMARY_GP;
                }

                [$secondaries, $regularError] = self::allocateSecondaries(
                    $residuals,
                    self::SECONDARY_SLOTS * count(self::GEAR_TYPES),
                );

                $error = $patternError + $regularError;
                if (!self::isCandidateAcceptable($best, $limit, $error)) {
                    continue;
                }

                $regularPrimaries = array_map(fn (int $i): string => $regularKeys[$i], $indices);

                // Stable sort: ties keep the rank order
                usort(
                    $regularPrimaries,
                    fn (string $a, string $b): int => $regulars[$b] <=> $regulars[$a],
                );

                $primaries = $pattern;
                foreach ($freeGearTypes as $i => $gearType) {
                    $primaries[$gearType] = $regularPrimaries[$i];
                }

                self::insertCandidate($best, $limit, [
                    'error' => $error,
                    'primaries' => $primaries,
                    'secondaries' => $secondaries,
                ]);
            }
        }

        $rankOrder = array_keys($abilities);
        return array_map(
            fn (array $candidate): array => [
                'error' => $candidate['error'],
                'config' => self::makeConfig(
                    $candidate['primaries'],
                    $candidate['secondaries'],
                    $rankOrder,
                ),
            ],
            $best,
        );
    }

    /**
     * Yields the patterns of the primary-only abilities: none or one of them for each gear type.
     *
     * @param array<string, array{gearType: string, average: float}> $primaryOnlys
     * @return Generator<array<string, string>> Gear type => ability key
     */
    private static function primaryOnlyPatterns(array $primaryOnlys, float $minRate): Generator
    {
        $options = [];
        foreach (self::GEAR_TYPES as $gearType) {
            $options[$gearType] = [null];
        }

        foreach ($primaryOnlys as $key => $info) {
            if (
                isset($options[$info['gearType']]) &&
                $info['average'] / self::PRIMARY_GP + self::EPSILON >= $minRate
            ) {
                $options[$info['gearType']][] = $key;
            }
        }

        $patterns = [[]];
        foreach ($options as $gearType => $keys) {
            $next = [];
            foreach ($patterns as $pattern) {
                foreach ($keys as $key) {
                    $next[] = $key === null ? $pattern : array_merge($pattern, [$gearType => $key]);
                }
            }
            $patterns = $next;
        }

        yield from $patterns;
    }

    /**
     * Allocates the secondaries greedily to the largest residual,
     * which minimizes the sum of the squared residuals.
     *
     * @param array<string, float> $residuals Ability key => residual GP, in rank order
     * @return array{array<string, int>, float} The numbers of the secondaries in rank order,
     *   and the sum of the squared residuals
     */
    private static function allocateSecondaries(array $residuals, int $slots): array
    {
        $counts = [];
        foreach ($residuals as $key => $_) {
            $counts[$key] = 0;
        }

        for ($n = 0; $n < $slots; ++$n) {
            $selected = null;
            $selectedValue = 0.0;
            foreach ($residuals as $key => $value) {
                if ($selected === null || $value > $selectedValue + self::EPSILON) {
                    $selected = $key;
                    $selectedValue = $value;
                }
            }

            if ($selected === null) {
                throw new LogicException();
            }

            $residuals[$selected] -= self::SECONDARY_GP;
            ++$counts[$selected];
        }

        $error = 0.0;
        foreach ($residuals as $value) {
            $error += $value * $value;
        }

        return [$counts, $error];
    }

    /**
     * @param list<array{error: float, primaries: array<string, string>, secondaries: array<string, int>}> $best
     */
    private static function isCandidateAcceptable(array $best, int $limit, float $error): bool
    {
        return count($best) < $limit || $error < $best[count($best) - 1]['error'] - self::EPSILON;
    }

    /**
     * Inserts the candidate keeping the list sorted. Ties keep the order of the insertion.
     *
     * @param list<array{error: float, primaries: array<string, string>, secondaries: array<string, int>}> $best
     * @param array{error: float, primaries: array<string, string>, secondaries: array<string, int>} $candidate
     * @param-out list<array{error: float, primaries: array<string, string>, secondaries: array<string, int>}> $best
     */
    private static function insertCandidate(array &$best, int $limit, array $candidate): void
    {
        $pos = count($best);
        foreach ($best as $i => $item) {
            if ($candidate['error'] < $item['error'] - self::EPSILON) {
                $pos = $i;
                break;
            }
        }

        array_splice($best, $pos, 0, [$candidate]);
        if (count($best) > $limit) {
            array_pop($best);
        }
    }

    /**
     * @param array<string, string> $primaries Gear type => ability key
     * @param array<string, int> $secondaryCounts Ability key => number of the secondaries, in rank order
     * @param list<string> $rankOrder
     * @return TypicalGearConfig3
     */
    private static function makeConfig(array $primaries, array $secondaryCounts, array $rankOrder): array
    {
        $orderedPrimaries = [];
        foreach (self::GEAR_TYPES as $gearType) {
            $orderedPrimaries[$gearType] = $primaries[$gearType] ?? throw new LogicException();
        }

        $secondaries = self::placeSecondaries($orderedPrimaries, $secondaryCounts, $rankOrder);

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
     * Returns non-decreasing index lists (combinations with repetition).
     *
     * @return list<list<int>>
     */
    private static function combinations(int $n, int $k): array
    {
        static $cache = [];

        return $cache["{$n}:{$k}"] ??= self::combinationsImpl($n, $k, 0);
    }

    /**
     * @return list<list<int>>
     */
    private static function combinationsImpl(int $n, int $k, int $start): array
    {
        if ($k === 0) {
            return [[]];
        }

        $results = [];
        for ($i = $start; $i < $n; ++$i) {
            foreach (self::combinationsImpl($n, $k - 1, $i) as $rest) {
                $results[] = [$i, ...$rest];
            }
        }

        return $results;
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
     * In each gear, the secondaries are ordered as follows:
     *
     * 1. The same ability as the primary
     * 2. Descending order of the total GP in the whole configuration
     * 3. Rank order
     *
     * @param array<string, string> $primaries Gear type => ability key
     * @param array<string, int> $counts Ability key => number of the secondaries, in rank order
     * @param list<string> $rankOrder
     * @return array<string, list<string>> Gear type => secondary ability keys
     */
    private static function placeSecondaries(array $primaries, array $counts, array $rankOrder): array
    {
        // Ordered by the total GP (stable sort: ties keep the rank order)
        $totals = [];
        foreach ($rankOrder as $key) {
            $totals[$key] = self::SECONDARY_GP * ($counts[$key] ?? 0);
        }
        foreach ($primaries as $key) {
            $totals[$key] = ($totals[$key] ?? 0) + self::PRIMARY_GP;
        }
        $displayOrder = $rankOrder;
        usort($displayOrder, fn (string $a, string $b): int => $totals[$b] <=> $totals[$a]);

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
                array_values(array_filter($displayOrder, fn (string $k): bool => $k !== $primaryKey)),
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
