<?php

/**
 * @copyright Copyright (C) 2015-2018 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\web;

use Yii;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;

trait HttpErrorTrait
{
    public static function error403(): void
    {
        throw new ForbiddenHttpException(
            Yii::t('yii', 'You are not allowed to perform this action.'),
        );
    }

    public static function error404(): void
    {
        throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
    }
}
