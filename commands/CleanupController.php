<?php

/**
 * @copyright Copyright (C) 2015-2019 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\commands;

use DateInterval;
use DateTime;
use DateTimeImmutable;
use DateTimeZone;
use Yii;
use app\models\UserLoginHistory;
use yii\console\Controller;

class CleanupController extends Controller
{
    public function actionLoginHistory(): int
    {
        $time = (new DateTimeImmutable(
            'now',
            new DateTimeZone(Yii::$app->timeZone),
        ))
            ->sub(new DateInterval('P30D'));

        UserLoginHistory::deleteAll(['and',
            ['<', 'created_at', $time->format(DateTime::ATOM)],
        ]);

        return 0;
    }
}
