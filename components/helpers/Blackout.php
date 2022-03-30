<?php

/**
 * @copyright Copyright (C) 2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\components\helpers;

use app\models\User;

final class Blackout
{
    /**
     * @param 'fest_normal'|'private'|'squad_2'|'squad_3'|'squad_4'|'standard' $lobbyKey
     * @param 'always'|'no'|'not-friend'|'not-private' $blackoutConfigValue
     * @param int<1, 8> $myPosition
     * @return int<1, 8>[]
     */
    public static function getBlackoutTargetList(
        string $lobbyKey,
        string $blackoutConfigValue,
        int $myPosition
    ): array {
        switch ($blackoutConfigValue) {
            // 誰も黒塗りしない
            case User::BLACKOUT_NOT_BLACKOUT:
                return [];

            // プラベでは黒塗りしない
            // プラベ以外では自分以外全て黒塗り
            case User::BLACKOUT_NOT_PRIVATE:
                if ($lobbyKey === 'private') {
                    return [];
                }

                return self::createList([$myPosition]);

            // プラベでは黒塗りしない
            // タッグマッチ(3-4人)では味方チームを黒塗りしない
            // それ以外では自分以外を黒塗り
            case User::BLACKOUT_NOT_FRIEND:
                if ($lobbyKey === 'private') {
                    return [];
                }

                if ($lobbyKey === 'squad_3' || $lobbyKey === 'squad_4') {
                    return $myPosition <= 4
                        ? self::createList([1, 2, 3, 4])
                        : self::createList([5, 6, 7, 8]);
                }
                return self::createList([$myPosition]);

            // 自分以外黒塗り
            case User::BLACKOUT_ALWAYS:
            default:
                return self::createList([$myPosition]);
        }
    }

    /**
     * @param int<1, 8>[] $except
     * @return int<1, 8>[]
     */
    private static function createList(array $except): array
    {
        return array_filter(
            range(1, 8),
            fn (int $pos): bool => !in_array($pos, $except, true),
        );
    }
}
