<?php

declare(strict_types=1);

namespace tests\helpers;

use Codeception\Test\Unit;
use app\components\helpers\Battle3WinLoseStats;

final class Battle3WinLoseStatsTest extends Unit
{
    private const array COUNTS = [
        'winKnockout' => 1,
        'winTime' => 2,
        'winUnknown' => 3,
        'loseKnockout' => 4,
        'loseTime' => 5,
        'loseUnknown' => 6,
    ];

    public function testFromCountsForRankedRule(): void
    {
        $this->assertSame(
            [
                'canKnockout' => true,
                'battles' => 21,
                'wins' => 6,
                ...self::COUNTS,
            ],
            Battle3WinLoseStats::fromCounts(self::COUNTS, 'gachi'),
        );
    }

    public function testFromCountsForTurfWarMovesEverythingToTime(): void
    {
        $this->assertSame(
            [
                'canKnockout' => false,
                'battles' => 21,
                'wins' => 6,
                'winKnockout' => 0,
                'winTime' => 6,
                'winUnknown' => 0,
                'loseKnockout' => 0,
                'loseTime' => 15,
                'loseUnknown' => 0,
            ],
            Battle3WinLoseStats::fromCounts(self::COUNTS, 'nawabari'),
        );
    }

    /**
     * Same as the Win % page (WinRateAction): only turf war is forced to "time"
     */
    public function testFromCountsForTricolorKeepsCounts(): void
    {
        $this->assertSame(
            [
                'canKnockout' => false,
                'battles' => 21,
                'wins' => 6,
                ...self::COUNTS,
            ],
            Battle3WinLoseStats::fromCounts(self::COUNTS, 'tricolor'),
        );
    }

    public function testFromCountsForUnknownRuleKeepsCounts(): void
    {
        $this->assertSame(
            [
                'canKnockout' => false,
                'battles' => 21,
                'wins' => 6,
                ...self::COUNTS,
            ],
            Battle3WinLoseStats::fromCounts(self::COUNTS, null),
        );
    }

    public function testSum(): void
    {
        $turf = Battle3WinLoseStats::fromCounts(self::COUNTS, 'nawabari');
        $ranked = Battle3WinLoseStats::fromCounts(self::COUNTS, 'gachi');

        $this->assertSame(
            [
                'canKnockout' => true,
                'battles' => 42,
                'wins' => 12,
                'winKnockout' => 1,
                'winTime' => 8,
                'winUnknown' => 3,
                'loseKnockout' => 4,
                'loseTime' => 20,
                'loseUnknown' => 6,
            ],
            Battle3WinLoseStats::sum([$turf, $ranked]),
        );

        $this->assertFalse(Battle3WinLoseStats::sum([$turf, $turf])['canKnockout']);
    }

    public function testSumOfEmptyList(): void
    {
        $this->assertSame(
            [
                'canKnockout' => false,
                'battles' => 0,
                'wins' => 0,
                'winKnockout' => 0,
                'winTime' => 0,
                'winUnknown' => 0,
                'loseKnockout' => 0,
                'loseTime' => 0,
                'loseUnknown' => 0,
            ],
            Battle3WinLoseStats::sum([]),
        );
    }

    /**
     * @dataProvider provideCompareByWinRate
     */
    public function testCompareByWinRate(int $expected, array $a, array $b): void
    {
        $this->assertSame(
            $expected,
            Battle3WinLoseStats::compareByWinRate(self::makeStats(...$a), self::makeStats(...$b)),
        );
    }

    public function provideCompareByWinRate(): array
    {
        return [
            'higher win rate first' => [-1, [2, 3], [1, 3]],
            'lower win rate last' => [1, [1, 3], [2, 3]],
            'win rate is compared, not wins' => [-1, [1, 1], [9, 10]],
            'same win rate, more battles first' => [-1, [2, 4], [1, 2]],
            'same win rate and battles' => [0, [1, 2], [1, 2]],
            'zero battles last' => [-1, [0, 1], [0, 0]],
        ];
    }

    private static function makeStats(int $wins, int $battles): array
    {
        return Battle3WinLoseStats::fromCounts(
            [
                'winKnockout' => 0,
                'winTime' => $wins,
                'winUnknown' => 0,
                'loseKnockout' => 0,
                'loseTime' => $battles - $wins,
                'loseUnknown' => 0,
            ],
            'gachi',
        );
    }
}
