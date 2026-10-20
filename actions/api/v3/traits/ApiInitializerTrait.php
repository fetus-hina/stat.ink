<?php

/**
 * @copyright Copyright (C) 2022-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

namespace app\actions\api\v3\traits;

use Yii;
use yii\web\Response;

trait ApiInitializerTrait
{
    protected function apiInit(?string $format = null): void
    {
        Yii::$app->language = 'en-US';
        Yii::$app->timeZone = 'Etc/UTC';
        Yii::$app->response->format = $format ?? Response::FORMAT_JSON;
    }
}
