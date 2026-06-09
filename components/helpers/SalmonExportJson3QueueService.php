<?php

/**
 * @copyright Copyright (C) 2023-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

namespace app\components\helpers;

use Throwable;
use Yii;
use app\components\helpers\db\Now;
use app\models\QueueSalmonExportJson3;
use app\models\User;
use jp3cki\uuid\Uuid;
use yii\db\Connection;

use function assert;

final class SalmonExportJson3QueueService
{
    private const LOCK_TIMEOUT_SEC = 1;

    public static function execute(QueueSalmonExportJson3 $queue): bool
    {
        $userModel = User::find()
            ->andWhere(['id' => $queue->user_id])
            ->limit(1)
            ->one();
        if (!$userModel) {
            return true;
        }

        $lock = null;
        try {
            $lock = CriticalSection::lock(
                SalmonExportJson3Helper::lockName($userModel),
                self::LOCK_TIMEOUT_SEC,
                Yii::$app->pgMutex,
            );

            SalmonExportJson3Helper::update($userModel);

            return true;
        } catch (Throwable $e) {
            Yii::warning(
                vsprintf('Failed to execute SalmonExportJson3QueueService: %s, user_id: %d', [
                    $e->getMessage(),
                    $userModel->id,
                ]),
                __METHOD__,
            );
        } finally {
            unset($lock);
        }

        return false;
    }

    public static function enqueue(User $user): void
    {
        $db = Yii::$app->db;
        assert($db instanceof Connection);

        $now = new Now();
        $params = [];
        $insertSql = $db->getQueryBuilder()->insert(
            QueueSalmonExportJson3::tableName(),
            [
                'id' => Uuid::v7()->formatAsString(),
                'user_id' => $user->id,
                'updated_at' => $now,
            ],
            $params,
        );

        // 同じ user_id が存在する場合は id と updated_at を更新する UPSERT。
        // このテーブルは id (PK) と user_id の 2 つの unique 制約を持つため、
        // QueryBuilder::upsert() の自動判定では競合対象を誤る。よって競合
        // ターゲットを user_id に明示する。
        $sql = vsprintf('%s ON CONFLICT (%s) DO UPDATE SET %s = EXCLUDED.%s, %s = EXCLUDED.%s', [
            $insertSql,
            $db->quoteColumnName('user_id'),
            $db->quoteColumnName('id'),
            $db->quoteColumnName('id'),
            $db->quoteColumnName('updated_at'),
            $db->quoteColumnName('updated_at'),
        ]);

        $db->createCommand($sql, $params)->execute();
    }
}
