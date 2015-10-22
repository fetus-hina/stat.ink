<?php
/**
 * @copyright Copyright (C) 2015 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\components\helpers;

class Battle
{
    public static function calcPeriod($unixTime)
    {
        // 2 * 3600: UTC 02:00 に切り替わるのでその分を引く
        // 4 * 3600: 4時間ごとにステージ変更
        return (int)floor(($unixTime - 2 * 3600) / (4 * 3600));
    }
}
