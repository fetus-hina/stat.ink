<?php

declare(strict_types=1);

namespace tests\helpers;

use Codeception\Test\Unit;
use app\components\helpers\Battle3EntireStatsHelper as H;

final class Battle3EntireStatsHelperTest extends Unit
{
    private const START_AT = 1_700_000_000;
    private const PAIR_CUP = 'TGVhZ3VlTWF0Y2hFdmVudC1QYWlyQ3Vw';

    /**
     * @dataProvider provideCases
     * @param array<string, mixed> $override
     * @param list<string> $expected
     */
    public function testDetectExclusionReasons(array $override, array $expected): void
    {
        $params = array_merge(
            [
                'isAutomated' => true,
                'startAt' => self::START_AT,
                'uploadedAt' => self::START_AT + 600,
                'lobbyKey' => 'xmatch',
                'ruleKey' => 'area',
                'eventInternalId' => null,
                'teamPlayerCounts' => [4, 4, 0],
                'ourTeamMeCount' => 1,
                'otherTeamsMeCount' => 0,
            ],
            $override,
        );

        $this->assertSame($expected, H::detectExclusionReasons(...$params));
    }

    public function provideCases(): array
    {
        $day = 86400;

        return [
            'usable' => [[], []],
            'not automated' => [
                ['isAutomated' => false],
                [H::REASON_NOT_AUTOMATED],
            ],
            'no rule' => [
                ['ruleKey' => null],
                [H::REASON_INCOMPLETE_DATA],
            ],
            'no start time' => [
                ['startAt' => null],
                [H::REASON_NO_START_TIME],
            ],
            'start time after upload' => [
                ['uploadedAt' => self::START_AT - 1],
                [H::REASON_FUTURE_START_TIME],
            ],
            'just at the max delay with another reason' => [
                ['uploadedAt' => self::START_AT + 7 * $day, 'isAutomated' => false],
                [H::REASON_NOT_AUTOMATED],
            ],
            'over the max delay with another reason' => [
                ['uploadedAt' => self::START_AT + 7 * $day + 1, 'isAutomated' => false],
                [H::REASON_NOT_AUTOMATED, H::REASON_UPLOADED_TOO_LATE],
            ],
            'over 24h without other reasons (legacy limit)' => [
                ['uploadedAt' => self::START_AT + $day + 1],
                [H::REASON_UPLOADED_TOO_LATE],
            ],
            'over 24h with another reason' => [
                ['uploadedAt' => self::START_AT + $day + 1, 'teamPlayerCounts' => [4, 3, 0]],
                [H::REASON_INVALID_PLAYERS],
            ],
            'just 24h without other reasons' => [
                ['uploadedAt' => self::START_AT + $day],
                [],
            ],
            'missing players' => [
                ['teamPlayerCounts' => [4, 0, 0]],
                [H::REASON_INVALID_PLAYERS],
            ],
            'no me' => [
                ['ourTeamMeCount' => 0],
                [H::REASON_INVALID_PLAYERS],
            ],
            'me in other team' => [
                ['otherTeamsMeCount' => 1],
                [H::REASON_INVALID_PLAYERS],
            ],
            'unexpected third team' => [
                ['teamPlayerCounts' => [4, 4, 4]],
                [H::REASON_INVALID_PLAYERS],
            ],
            'tricolor' => [
                [
                    'lobbyKey' => 'splatfest_open',
                    'ruleKey' => 'tricolor',
                    'teamPlayerCounts' => [4, 2, 2],
                ],
                [],
            ],
            'tricolor with missing players' => [
                [
                    'lobbyKey' => 'splatfest_open',
                    'ruleKey' => 'tricolor',
                    'teamPlayerCounts' => [4, 2, 1],
                ],
                [H::REASON_INVALID_PLAYERS],
            ],
            'event' => [
                ['lobbyKey' => 'event', 'eventInternalId' => 'dummy'],
                [],
            ],
            'unknown event' => [
                ['lobbyKey' => 'event', 'teamPlayerCounts' => [4, 0, 0]],
                [H::REASON_UNKNOWN_EVENT],
            ],
            'pair cup' => [
                [
                    'lobbyKey' => 'event',
                    'eventInternalId' => self::PAIR_CUP,
                    'teamPlayerCounts' => [2, 2, 0],
                ],
                [],
            ],
            'pair cup with 4v4' => [
                ['lobbyKey' => 'event', 'eventInternalId' => self::PAIR_CUP],
                [H::REASON_INVALID_PLAYERS],
            ],
            'multiple reasons' => [
                ['isAutomated' => false, 'startAt' => null, 'teamPlayerCounts' => [0, 0, 0]],
                [H::REASON_NOT_AUTOMATED, H::REASON_NO_START_TIME, H::REASON_INVALID_PLAYERS],
            ],
        ];
    }
}
