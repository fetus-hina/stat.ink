<?php

/**
 * @copyright Copyright (C) 2016-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\components\ability;

use Yii;
use app\models\Battle;

use function version_compare;

class Effect
{
    public static function factory(Battle $battle)
    {
        $gameVersion = $battle->splatoonVersion->tag ?? null;
        if (!$gameVersion) {
            return null;
        }
        $ns = __NAMESPACE__ . '\effect';
        $list = [
            '2.9.0' => "{$ns}\\v020900",
            '2.8.0' => "{$ns}\\v020800",
            '2.7.0' => "{$ns}\\v020700",
            '2.6.0' => "{$ns}\\v020600",
            '2.5.0' => "{$ns}\\v020500",
        ];
        foreach ($list as $classVersion => $className) {
            if (version_compare($classVersion, $gameVersion, '<=')) {
                return Yii::createObject([
                    'class' => $className,
                    'battle' => $battle,
                    'version' => $gameVersion,
                ]);
            }
        }
        return null;
    }
}
