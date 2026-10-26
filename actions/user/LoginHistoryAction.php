<?php

/**
 * @copyright Copyright (C) 2018-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

namespace app\actions\user;

use DateInterval;
use DateTime;
use DateTimeImmutable;
use DateTimeZone;
use Override;
use Yii;
use yii\data\ActiveDataProvider;
use yii\data\Pagination;
use yii\web\ServerErrorHttpException;
use yii\web\ViewAction;

class LoginHistoryAction extends ViewAction
{
    #[Override]
    public function run(): string
    {
        if (!$user = Yii::$app->getUser()->getIdentity()) {
            throw new ServerErrorHttpException();
        }

        $time = new DateTimeImmutable(
            'now',
            new DateTimeZone(Yii::$app->timeZone),
        )
            ->sub(new DateInterval('P30D'));

        return $this->controller->render('login-history', [
            'dataProvider' => new ActiveDataProvider([
                'query' => $user->getLoginHistories()
                    ->with([
                        'method',
                        'userAgent',
                    ])
                    ->where(['>=', 'created_at', $time->format(DateTime::ATOM)]),
                'pagination' => Yii::createObject([
                    'class' => Pagination::class,
                    'pageSize' => 50,
                ]),
                'sort' => false,
                'key' => 'pseudoId',
            ]),
        ]);
    }
}
