<?php

/**
 * @copyright Copyright (C) 2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

namespace app\components\helpers;

use yii\db\Query;

/**
 * Conditions to find battles that won an Anarchy (Series) or X Battle series
 * in the same way as the in-game badges count them.
 */
final class Battle3SeriesWin
{
    public const string LOBBY_BANKARA_CHALLENGE = 'bankara_challenge';
    public const string LOBBY_XMATCH = 'xmatch';

    /**
     * Number of wins required to win an Anarchy (Series) series
     */
    public const int BANKARA_CHALLENGE_WINS = 5;

    /**
     * Number of wins required to win an X Battle series
     */
    public const int XMATCH_WINS = 3;

    /**
     * Minimum X Power (at the beginning of the series) counted by the X Battle badges
     */
    public const int XMATCH_MIN_X_POWER = 2000;

    /**
     * The first game version which counts the series win badges
     */
    public const string MIN_VERSION = '6.0.0';

    /**
     * Returns a condition (for `andWhere()`) that matches the final battles of the
     * won series of the specified lobbies.
     *
     * The query must refer the battle table as `{{%battle3}}`.
     *
     * @param list<self::LOBBY_*> $lobbies
     * @return array<mixed>
     */
    public static function condition(
        array $lobbies = [self::LOBBY_BANKARA_CHALLENGE, self::LOBBY_XMATCH],
    ): array {
        if (!$lobbies) {
            // An empty "or" condition is ignored by Yii and matches everything
            return ['and', '1 <> 1'];
        }

        $conditions = ['or'];
        foreach ($lobbies as $lobby) {
            $conditions[] = match ($lobby) {
                self::LOBBY_BANKARA_CHALLENGE => self::bankaraChallengeCondition(),
                self::LOBBY_XMATCH => self::xmatchCondition(),
            };
        }

        return $conditions;
    }

    /**
     * @return array<mixed>
     */
    private static function bankaraChallengeCondition(): array
    {
        // The rank-up battles are finished with 3 wins, so they never match.
        return ['and',
            self::lobbyCondition(self::LOBBY_BANKARA_CHALLENGE),
            self::winCondition(),
            ['{{%battle3}}.[[challenge_win]]' => self::BANKARA_CHALLENGE_WINS],
            self::versionCondition(),
        ];
    }

    /**
     * @return array<mixed>
     */
    private static function xmatchCondition(): array
    {
        // The battles in the placement (calculation) series have no X Power,
        // so they never match.
        return ['and',
            self::lobbyCondition(self::LOBBY_XMATCH),
            self::winCondition(),
            ['{{%battle3}}.[[challenge_win]]' => self::XMATCH_WINS],
            ['>=', '{{%battle3}}.[[x_power_before]]', self::XMATCH_MIN_X_POWER],
            self::versionCondition(),
        ];
    }

    /**
     * @return array<mixed>
     */
    private static function versionCondition(): array
    {
        return [
            '{{%battle3}}.[[version_id]]' => new Query()
                ->select(['id'])
                ->from('{{%splatoon_version3}}')
                ->andWhere(['>=', 'release_at', new Query()
                    ->select(['release_at'])
                    ->from('{{%splatoon_version3}}')
                    ->andWhere(['tag' => self::MIN_VERSION]),
                ]),
        ];
    }

    /**
     * @return array<mixed>
     */
    private static function lobbyCondition(string $key): array
    {
        return [
            '{{%battle3}}.[[lobby_id]]' => new Query()
                ->select(['id'])
                ->from('{{%lobby3}}')
                ->andWhere(['key' => $key]),
        ];
    }

    /**
     * @return array<mixed>
     */
    private static function winCondition(): array
    {
        return [
            '{{%battle3}}.[[result_id]]' => new Query()
                ->select(['id'])
                ->from('{{%result3}}')
                ->andWhere(['key' => 'win']),
        ];
    }
}
