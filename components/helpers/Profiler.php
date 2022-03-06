<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\components\helpers;

use Yii;

final class Profiler
{
    public static function profile(string $message, string $category): Resource
    {
        Yii::beginProfile($message, $category);

        return new Resource(
            true, // no meaning
            fn () => Yii::endProfile($message, $category),
        );
    }
}
