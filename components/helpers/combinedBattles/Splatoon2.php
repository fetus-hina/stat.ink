<?php

/**
 * @copyright Copyright (C) 2022-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\helpers\combinedBattles;

use DateTime;
use DateTimeImmutable;
use app\models\Battle2;
use app\models\Salmon2;
use yii\db\ActiveQuery;

use const SORT_DESC;

return [
    [
        'query' => Battle2::find()
            ->innerJoinWith('user', true)
            ->with([
                'battleImageResult',
                'lobby',
                'map',
                'mode',
                'rule',
                'user.userIcon',
            ])
            ->andWhere([
                '{{user}}.[[hide_data_on_toppage]]' => false,
            ])
            ->limit($num ?? 100)
            ->orderBy(['battle2.id' => SORT_DESC]),
        'callback' => function (ActiveQuery $q, ?DateTimeImmutable $t): void {
            if (!$t) {
                return;
            }
            $q->andWhere(['>=', 'battle2.created_at', $t->format(DateTime::ATOM)]);
        },
    ],
    [
        'query' => Salmon2::find()
            ->innerJoinWith('user', true)
            ->with([
                'stage',
                'user.userIcon',
            ])
            ->andWhere([
                '{{user}}.[[hide_data_on_toppage]]' => false,
            ])
            ->limit($num ?? 100)
            ->orderBy(['salmon2.id' => SORT_DESC]),
        'callback' => function (ActiveQuery $q, ?DateTimeImmutable $t): void {
            if (!$t) {
                return;
            }
            $q->andWhere(['>=', 'salmon2.created_at', $t->format(DateTime::ATOM)]);
        },
    ],
];
