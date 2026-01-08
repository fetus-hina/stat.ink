<?php

/**
 * @copyright Copyright (C) 2023-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\helpers\salmonStatsV3;

use DateTimeImmutable;
use Exception;
use Throwable;
use Yii;
use app\models\User;
use app\models\UserStatEggstraWork3;
use yii\db\Connection;
use yii\db\Query;

use function vsprintf;

trait EggstraWorkTrait
{
    protected static function createEggstraWorkStats(
        Connection $db,
        User $user,
        DateTimeImmutable $now,
    ): bool {
        try {
            UserStatEggstraWork3::deleteAll([
                'user_id' => $user->id,
            ]);

            foreach (self::makeEggstraWorkStats($db, $user, $now) as $row) {
                $model = Yii::createObject(UserStatEggstraWork3::class);
                $model->attributes = $row;
                if (!$model->save()) {
                    throw new Exception('Failed to save');
                }
            }

            return true;
        } catch (Throwable $e) {
            Yii::error(
                vsprintf('Catch %s, message=%s', [
                    $e::class,
                    $e->getMessage(),
                ]),
                __METHOD__,
            );
            $db->transaction->rollBack();
            return false;
        }
    }

    private static function makeEggstraWorkStats(
        Connection $db,
        User $user,
        DateTimeImmutable $now,
    ): array {
        return (new Query())
            ->select([
                'user_id' => '{{%salmon3}}.[[user_id]]',
                'schedule_id' => '{{%salmon3}}.[[schedule_id]]',
                'golden_eggs' => 'MAX({{%salmon3}}.[[golden_eggs]])',
            ])
            ->from('{{%salmon3}}')
            ->innerJoin(
                '{{%salmon_schedule3}}',
                '{{%salmon3}}.[[schedule_id]] = {{%salmon_schedule3}}.[[id]]',
            )
            ->andWhere(['and',
                [
                    '{{%salmon3}}.[[is_automated]]' => true,
                    '{{%salmon3}}.[[is_deleted]]' => false,
                    '{{%salmon3}}.[[is_eggstra_work]]' => true,
                    '{{%salmon3}}.[[is_private]]' => false,
                    '{{%salmon3}}.[[user_id]]' => $user->id,
                    '{{%salmon_schedule3}}.[[is_eggstra_work]]' => true,
                ],
                ['not', ['{{%salmon3}}.[[golden_eggs]]' => null]],
                ['>=', '{{%salmon3}}.[[golden_eggs]]', 0],
            ])
            ->groupBy([
                '{{%salmon3}}.[[user_id]]',
                '{{%salmon3}}.[[schedule_id]]',
            ])
            ->all($db);
    }
}
