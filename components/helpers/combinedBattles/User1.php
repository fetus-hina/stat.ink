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
use app\models\Battle;
use yii\db\ActiveQuery;

use const SORT_DESC;

return [
    [
        'query' => Battle::find()
            ->andWhere(['user_id' => $user->id])
            ->with([
                'battleImageResult',
                'map',
                'rule',
                'user',
                'user.userIcon',
            ])
            ->limit($num ?? 100)
            ->orderBy(['battle.id' => SORT_DESC]),
        'callback' => function (ActiveQuery $q, ?DateTimeImmutable $t): void {
            if (!$t) {
                return;
            }
            $q->andWhere(['>=', 'battle.at', $t->format(DateTime::ATOM)]);
        },
    ],
];
