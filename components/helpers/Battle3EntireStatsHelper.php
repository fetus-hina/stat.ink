<?php

/**
 * @copyright Copyright (C) 2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

namespace app\components\helpers;

use app\models\Battle3;
use app\models\BattleTricolorPlayer3;

use function array_sum;
use function in_array;
use function strtotime;

/**
 * バトルが全体統計に使用されない理由を推定する
 *
 * 投稿時の判定 (PostBattleForm::isUsableForEntireStats) の結果は理由を保存していないため、
 * 保存済みのデータから表示用に理由を推定する。
 */
final class Battle3EntireStatsHelper
{
    public const REASON_NOT_AUTOMATED = 'not-automated';
    public const REASON_INCOMPLETE_DATA = 'incomplete-data';
    public const REASON_NO_START_TIME = 'no-start-time';
    public const REASON_FUTURE_START_TIME = 'future-start-time';
    public const REASON_UPLOADED_TOO_LATE = 'uploaded-too-late';
    public const REASON_UNKNOWN_EVENT = 'unknown-event';
    public const REASON_INVALID_PLAYERS = 'invalid-players';

    /**
     * 以前の最大許容遅延時間 (秒)
     */
    private const LEGACY_MAX_DELAY = 86400;

    private const PAIR_CUP_EVENT_ID = 'TGVhZ3VlTWF0Y2hFdmVudC1QYWlyQ3Vw';

    /**
     * @return list<string>
     */
    public static function getExclusionReasons(Battle3 $model): array
    {
        $rule = $model->rule;
        $isTricolor = $rule?->key === 'tricolor';

        $teamPlayerCounts = [0, 0, 0];
        $ourTeamMeCount = 0;
        $otherTeamsMeCount = 0;
        $players = $isTricolor ? $model->battleTricolorPlayer3s : $model->battlePlayer3s;
        foreach ($players as $player) {
            $teamIndex = $player instanceof BattleTricolorPlayer3
                ? $player->team - 1
                : ($player->is_our_team ? 0 : 1);
            if ($teamIndex < 0 || $teamIndex > 2) {
                continue;
            }

            ++$teamPlayerCounts[$teamIndex];
            if ($player->is_me) {
                if ($teamIndex === 0) {
                    ++$ourTeamMeCount;
                } else {
                    ++$otherTeamsMeCount;
                }
            }
        }

        return self::detectExclusionReasons(
            isAutomated: (bool)$model->is_automated,
            startAt: $model->start_at !== null ? (strtotime($model->start_at) ?: null) : null,
            uploadedAt: strtotime($model->created_at) ?: 0,
            lobbyKey: $model->lobby?->key,
            ruleKey: $rule?->key,
            eventInternalId: $model->event?->internal_id,
            teamPlayerCounts: $teamPlayerCounts,
            ourTeamMeCount: $ourTeamMeCount,
            otherTeamsMeCount: $otherTeamsMeCount,
        );
    }

    /**
     * @param array{int, int, int} $teamPlayerCounts 自チーム・相手チーム・第3チームの人数
     * @return list<string>
     */
    public static function detectExclusionReasons(
        bool $isAutomated,
        ?int $startAt,
        int $uploadedAt,
        ?string $lobbyKey,
        ?string $ruleKey,
        ?string $eventInternalId,
        array $teamPlayerCounts,
        int $ourTeamMeCount,
        int $otherTeamsMeCount,
    ): array {
        $reasons = [];

        if (!$isAutomated) {
            $reasons[] = self::REASON_NOT_AUTOMATED;
        }

        if ($lobbyKey === null || $ruleKey === null) {
            $reasons[] = self::REASON_INCOMPLETE_DATA;
        }

        $delay = null;
        if ($startAt === null) {
            $reasons[] = self::REASON_NO_START_TIME;
        } elseif ($startAt > $uploadedAt) {
            $reasons[] = self::REASON_FUTURE_START_TIME;
        } else {
            $delay = $uploadedAt - $startAt;
            if ($delay > Battle3Helper::ENTIRE_STATS_MAX_DELAY_DAYS * 86400) {
                $reasons[] = self::REASON_UPLOADED_TOO_LATE;
            }
        }

        if ($lobbyKey === 'event' && $eventInternalId === null) {
            $reasons[] = self::REASON_UNKNOWN_EVENT;
        } elseif (
            $lobbyKey !== null &&
            $ruleKey !== null &&
            !self::isValidPlayers(
                $lobbyKey,
                $ruleKey,
                $eventInternalId,
                $teamPlayerCounts,
                $ourTeamMeCount,
                $otherTeamsMeCount,
            )
        ) {
            $reasons[] = self::REASON_INVALID_PLAYERS;
        }

        // 以前は最大許容遅延時間が 24 時間だったため、その頃に投稿されたバトルは
        // 他に理由が見当たらなければ投稿が遅かったためと推定する
        if (!$reasons && $delay !== null && $delay > self::LEGACY_MAX_DELAY) {
            $reasons[] = self::REASON_UPLOADED_TOO_LATE;
        }

        return $reasons;
    }

    /**
     * @param array{int, int, int} $teamPlayerCounts
     */
    private static function isValidPlayers(
        string $lobbyKey,
        string $ruleKey,
        ?string $eventInternalId,
        array $teamPlayerCounts,
        int $ourTeamMeCount,
        int $otherTeamsMeCount,
    ): bool {
        [$expectTotalCount, $expectPerTeamCount] = match (true) {
            $lobbyKey === 'event' && $eventInternalId === self::PAIR_CUP_EVENT_ID => [4, [2]],
            $lobbyKey === 'splatfest_open' && $ruleKey === 'tricolor' => [8, [2, 4]],
            default => [8, [4]],
        };

        [$ours, $theirs, $third] = $teamPlayerCounts;

        return $ourTeamMeCount === 1 &&
            $otherTeamsMeCount === 0 &&
            in_array($ours, $expectPerTeamCount, true) &&
            in_array($theirs, $expectPerTeamCount, true) &&
            ($third === 0 || in_array($third, $expectPerTeamCount, true)) &&
            array_sum($teamPlayerCounts) === $expectTotalCount;
    }
}
