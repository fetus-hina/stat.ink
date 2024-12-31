<?php

/**
 * @copyright Copyright (C) 2022-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\models\api\v3\postBattle;

use Yii;
use app\components\helpers\CriticalSection;
use app\models\SplashtagTitle3;

use function sprintf;

trait SplashtagTrait
{
    use TypeHelperTrait;

    protected static function splashtagTitle(?string $title): ?int
    {
        $title = self::strVal($title);
        if ($title === null) {
            return null;
        }

        // Find with Double-checked locking pattern
        $model = SplashtagTitle3::findOne(['name' => $title]);
        if (!$model) {
            $lock = CriticalSection::lock(SplashtagTitle3::class, 60);
            try {
                $model = SplashtagTitle3::findOne(['name' => $title]);
                if (!$model) {
                    // Not registered. Create it!
                    $model = Yii::createObject([
                        'class' => SplashtagTitle3::class,
                        'name' => $title,
                    ]);
                    if (!$model->save()) {
                        return null;
                    }
                }
            } finally {
                unset($lock);
            }
        }

        return (int)$model->id;
    }

    protected static function hashNumberVal($value): ?string
    {
        // もし3桁以下の数字だったら0埋めする
        $intVal = self::intVal($value);
        if ($intVal && $intVal < 1000) {
            return sprintf('%04d', $intVal);
        }

        return self::strVal($value);
    }
}
