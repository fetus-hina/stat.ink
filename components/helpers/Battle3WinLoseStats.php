<?php

/**
 * @copyright Copyright (C) 2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

namespace app\components\helpers;

/**
 * Win/lose counts of Splatoon 3 battles, broken down by knockout
 *
 * @phpstan-type WinLoseCounts array{
 *   winKnockout: int,
 *   winTime: int,
 *   winUnknown: int,
 *   loseKnockout: int,
 *   loseTime: int,
 *   loseUnknown: int
 * }
 * @phpstan-type WinLoseStats array{
 *   canKnockout: bool,
 *   battles: int,
 *   wins: int,
 *   winKnockout: int,
 *   winTime: int,
 *   winUnknown: int,
 *   loseKnockout: int,
 *   loseTime: int,
 *   loseUnknown: int
 * }
 */
final class Battle3WinLoseStats
{
    /**
     * Counters: `[key => [isWin, isKnockout]]`
     */
    public const array COUNTERS = [
        'winKnockout' => [true, true],
        'winTime' => [true, false],
        'winUnknown' => [true, null],
        'loseKnockout' => [false, true],
        'loseTime' => [false, false],
        'loseUnknown' => [false, null],
    ];

    /**
     * @param WinLoseCounts $counts
     * @param string|null $ruleGroupKey key of rule_group3, or null if the rule is unknown
     * @return WinLoseStats
     */
    public static function fromCounts(array $counts, ?string $ruleGroupKey): array
    {
        if ($ruleGroupKey === 'nawabari') {
            // Turf War battles always end in time.
            // Same as WinRateAction does. It matters when they are summed with ranked battles.
            $counts = [
                'winKnockout' => 0,
                'winTime' => $counts['winKnockout'] + $counts['winTime'] + $counts['winUnknown'],
                'winUnknown' => 0,
                'loseKnockout' => 0,
                'loseTime' => $counts['loseKnockout'] + $counts['loseTime'] + $counts['loseUnknown'],
                'loseUnknown' => 0,
            ];
        }

        $wins = $counts['winKnockout'] + $counts['winTime'] + $counts['winUnknown'];
        $loses = $counts['loseKnockout'] + $counts['loseTime'] + $counts['loseUnknown'];

        return [
            'canKnockout' => $ruleGroupKey === 'gachi',
            'battles' => $wins + $loses,
            'wins' => $wins,
            'winKnockout' => $counts['winKnockout'],
            'winTime' => $counts['winTime'],
            'winUnknown' => $counts['winUnknown'],
            'loseKnockout' => $counts['loseKnockout'],
            'loseTime' => $counts['loseTime'],
            'loseUnknown' => $counts['loseUnknown'],
        ];
    }

    /**
     * @param list<WinLoseStats> $list
     * @return WinLoseStats
     */
    public static function sum(array $list): array
    {
        $ret = [
            'canKnockout' => false,
            'battles' => 0,
            'wins' => 0,
            'winKnockout' => 0,
            'winTime' => 0,
            'winUnknown' => 0,
            'loseKnockout' => 0,
            'loseTime' => 0,
            'loseUnknown' => 0,
        ];

        foreach ($list as $stats) {
            $ret['canKnockout'] = $ret['canKnockout'] || $stats['canKnockout'];
            $ret['battles'] += $stats['battles'];
            $ret['wins'] += $stats['wins'];
            $ret['winKnockout'] += $stats['winKnockout'];
            $ret['winTime'] += $stats['winTime'];
            $ret['winUnknown'] += $stats['winUnknown'];
            $ret['loseKnockout'] += $stats['loseKnockout'];
            $ret['loseTime'] += $stats['loseTime'];
            $ret['loseUnknown'] += $stats['loseUnknown'];
        }

        return $ret;
    }

    /**
     * Comparator: higher winning percentage first, then more battles first
     *
     * @param WinLoseStats $a
     * @param WinLoseStats $b
     */
    public static function compareByWinRate(array $a, array $b): int
    {
        // Compare wins/battles without division (battles may be zero)
        return $b['wins'] * $a['battles'] <=> $a['wins'] * $b['battles']
            ?: $b['battles'] <=> $a['battles'];
    }
}
