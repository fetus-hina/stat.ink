<?php

/**
 * @copyright Copyright (C) 2016-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\components\helpers;

use app\models\User;

use function array_filter;
use function in_array;
use function range;

class Blackout
{
    public static function getBlackoutTargetList($lobbyKey, $blackoutConfigValue, $myPosition): array
    {
        if ($myPosition === false) {
            return [];
        }
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
                return static::createList([$myPosition]);

            // プラベでは黒塗りしない
            // タッグマッチ(3-4人)では味方チームを黒塗りしない
            // それ以外では自分以外を黒塗り
            case User::BLACKOUT_NOT_FRIEND:
                if ($lobbyKey === 'private') {
                    return [];
                }
                if ($lobbyKey === 'squad_3' || $lobbyKey === 'squad_4') {
                    return $myPosition <= 4
                        ? static::createList([1, 2, 3, 4])
                        : static::createList([5, 6, 7, 8]);
                }
                return static::createList([$myPosition]);

            // 自分以外黒塗り
            case User::BLACKOUT_ALWAYS:
            default:
                return static::createList([$myPosition]);
        }
    }

    private static function createList(array $except): array
    {
        return array_filter(
            range(1, 8),
            fn ($pos) => !in_array($pos, $except),
        );
    }
}
