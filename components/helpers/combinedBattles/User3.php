<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\helpers\combinedBattles;

use DateTime;
use DateTimeImmutable;
use app\models\Battle3;
use app\models\Salmon3;
use yii\db\ActiveQuery;

use const SORT_DESC;

return [
    [
        'query' => Battle3::find()
            ->andWhere([
                'user_id' => $user->id,
                'is_deleted' => false,
            ])
            ->with([
                'battleImageResult3',
                'lobby',
                'map',
                'result',
                'rule',
                'user',
                'user.userIcon',
            ])
            ->limit($num ?? 100)
            ->orderBy([
                '{{%battle3}}.[[end_at]]' => SORT_DESC,
            ]),
        'callback' => function (ActiveQuery $q, ?DateTimeImmutable $t): void {
            if (!$t) {
                return;
            }
            $q->andWhere(['>=', '{{battle3}}.[[created_at]]', $t->format(DateTime::ATOM)]);
        },
    ],
    [
        'query' => Salmon3::find()
            ->andWhere([
                'user_id' => $user->id,
                'is_deleted' => false,
            ])
            ->with([
                'bigStage',
                'kingSalmonid',
                'stage',
                'user',
                'user.userIcon',
            ])
            ->limit($num ?? 100)
            ->orderBy([
                '{{%salmon3}}.[[start_at]]' => SORT_DESC,
            ]),
        'callback' => function (ActiveQuery $q, ?DateTimeImmutable $t): void {
            if (!$t) {
                return;
            }
            $q->andWhere(['>=', '{{%salmon3}}.[[created_at]]', $t->format(DateTime::ATOM)]);
        },
    ],
];
