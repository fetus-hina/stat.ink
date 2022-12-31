<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\helpers\userStatsV3;

use DateTimeImmutable;
use Yii;
use app\models\User;
use app\models\UserWeapon3;
use yii\db\Connection;
use yii\db\Exception as DbException;
use yii\db\Query;

trait WeaponTrait
{
    protected static function createWeaponStats(
        Connection $db,
        User $user,
        DateTimeImmutable $now
    ): bool {
        try {
            UserWeapon3::deleteAll(['user_id' => $user->id]);

            $query = (new Query())
                ->select([
                    'user_id',
                    'weapon_id',
                    'battles' => 'COUNT(*)',
                    'last_used_at' => 'MAX({{%battle3}}.[[end_at]])',
                ])
                ->from('{{%battle3}}')
                ->andWhere([
                    'is_deleted' => false,
                    'user_id' => $user->id,
                ])
                ->andWhere(['not', ['weapon_id' => null]])
                ->groupBy(['user_id', 'weapon_id']);

            $sql = \vsprintf('INSERT INTO %s ( %s ) %s', [
                $db->quoteTableName(UserWeapon3::tableName()),
                \implode(', ', \array_map(
                    fn (string $column): string => $db->quoteColumnName($column),
                    ['user_id', 'weapon_id', 'battles', 'last_used_at'],
                )),
                $query->createCommand($db)->rawSql,
            ]);
            $db->createCommand($sql)->execute();

            return true;
        } catch (DbException $e) {
            Yii::error(
                \vsprintf('Catch %s, message=%s', [
                    $e::class,
                    $e->getMessage(),
                ]),
                __METHOD__,
            );
            $db->transaction->rollBack();
            return false;
        }
    }
}
