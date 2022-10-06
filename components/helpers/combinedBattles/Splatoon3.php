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
use yii\db\ActiveQuery;

use const SORT_DESC;

return [
    [
        'query' => Battle3::find()
            ->with([
                'battleImageResult3',
                'lobby',
                'map',
                'result',
                'rule',
                'user',
                'user.userIcon',
            ])
            ->andWhere(['{{%battle3}}.[[is_deleted]]' => false])
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
];
