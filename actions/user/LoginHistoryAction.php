<?php

/**
 * @copyright Copyright (C) 2015-2018 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\actions\user;

use DateInterval;
use DateTimeImmutable;
use DateTimeZone;
use Yii;
use yii\base\Action;
use yii\data\ActiveDataProvider;

final class LoginHistoryAction extends Action
{
    public function run(): string
    {
        $user = Yii::$app->user->identity;

        $time = (new DateTimeImmutable('now', new DateTimeZone(Yii::$app->timeZone)))
            ->sub(new DateInterval('P30D'));

        return $this->controller->render('login-history', [
            'dataProvider' => new ActiveDataProvider([
                'query' => $user->getLoginHistories()
                    ->with(['method', 'userAgent'])
                    ->where(['>=', 'created_at', $time->format(DateTimeImmutable::ATOM)]),
                'pagination' => [
                    'pageSize' => 50,
                ],
                'sort' => false,
                'key' => 'pseudoId',
            ]),
        ]);
    }
}
