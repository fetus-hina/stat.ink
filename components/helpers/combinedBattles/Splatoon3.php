<?php

/**
 * @copyright Copyright (C) 2015-2024 AIZAWA Hina
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
            ->innerJoinWith('user', true)
            ->with([
                'battleImageResult3',
                'event',
                'lobby',
                'map',
                'result',
                'rule',
                'user.userIcon',
            ])
            ->andWhere([
                '{{%battle3}}.[[is_deleted]]' => false,
                '{{user}}.[[hide_data_on_toppage]]' => false,
            ])
            ->limit($num ?? 100)
            ->orderBy([
                '{{%battle3}}.[[id]]' => SORT_DESC,
            ]),
        'callback' => function (ActiveQuery $q, ?DateTimeImmutable $t): void {
            if (!$t) {
                return;
            }
            $q->andWhere(['>=', '{{%battle3}}.[[created_at]]', $t->format(DateTime::ATOM)]);
        },
    ],
    [
        'query' => Salmon3::find()
            ->innerJoinWith('user', true)
            ->with([
                'bigStage',
                'kingSalmonid',
                'stage',
                'user.userIcon',
            ])
            ->andWhere([
                '{{%salmon3}}.[[is_deleted]]' => false,
                '{{user}}.[[hide_data_on_toppage]]' => false,
            ])
            ->limit($num ?? 100)
            ->orderBy([
                '{{%salmon3}}.[[id]]' => SORT_DESC,
            ]),
        'callback' => function (ActiveQuery $q, ?DateTimeImmutable $t): void {
            if (!$t) {
                return;
            }
            $q->andWhere(['>=', '{{%salmon3}}.[[created_at]]', $t->format(DateTime::ATOM)]);
        },
    ],
];
