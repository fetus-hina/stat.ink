<?php

/**
 * @copyright Copyright (C) 2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\ability;

use Yii;
use app\components\ability\effect\Base as EffectDetails;
use app\components\ability\effect\v020500;
use app\components\ability\effect\v020600;
use app\components\ability\effect\v020700;
use app\components\ability\effect\v020800;
use app\components\ability\effect\v020900;
use app\models\Battle;

final class Effect
{
    public static function factory(Battle $battle): ?EffectDetails
    {
        $gameVersion = $battle->splatoonVersion->tag ?? null;
        if (!$gameVersion) {
            return null;
        }

        $list = [
            '2.9.0' => v020900::class,
            '2.8.0' => v020800::class,
            '2.7.0' => v020700::class,
            '2.6.0' => v020600::class,
            '2.5.0' => v020500::class,
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
