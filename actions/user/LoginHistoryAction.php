<?php

/**
 * @copyright Copyright (C) 2018-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\actions\user;

use DateInterval;
use DateTime;
use DateTimeImmutable;
use DateTimeZone;
use Yii;
use yii\data\ActiveDataProvider;
use yii\web\ViewAction;

class LoginHistoryAction extends ViewAction
{
    public function run()
    {
        $user = Yii::$app->getUser()->getIdentity();

        $time = (new DateTimeImmutable(
            'now',
            new DateTimeZone(Yii::$app->timeZone),
        ))
            ->sub(new DateInterval('P30D'));

        return $this->controller->render('login-history', [
            'dataProvider' => new ActiveDataProvider([
                'query' => $user->getLoginHistories()
                    ->with([
                        'method',
                        'userAgent',
                    ])
                    ->where(['>=', 'created_at', $time->format(DateTime::ATOM)]),
                'pagination' => [
                    'pageSize' => 50,
                ],
                'sort' => false,
                'key' => 'pseudoId',
            ]),
        ]);
    }
}
