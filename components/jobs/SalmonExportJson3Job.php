<?php

/**
 * @copyright Copyright (C) 2023-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

namespace app\components\jobs;

use Yii;
use app\components\helpers\CriticalSection;
use app\components\helpers\SalmonExportJson3Helper;
use app\components\helpers\db\Now;
use app\models\QueueSalmonExportJson3;
use app\models\User;
use jp3cki\uuid\Uuid;
use yii\base\BaseObject;
use yii\db\Connection;
use yii\queue\RetryableJobInterface;

use function assert;

final class SalmonExportJson3Job extends BaseObject implements RetryableJobInterface
{
    use JobPriority;

    private const LOCK_TIMEOUT_SEC = 180;

    public int $user;

    /**
     * @inheritdoc
     */
    public function execute($queue)
    {
        $userModel = User::find()
            ->andWhere(['id' => $this->user])
            ->limit(1)
            ->one();
        if (!$userModel) {
            return;
        }

        $lock = CriticalSection::lock(
            SalmonExportJson3Helper::lockName($userModel),
            self::LOCK_TIMEOUT_SEC,
            Yii::$app->pgMutex,
        );
        try {
            SalmonExportJson3Helper::update($userModel);
        } finally {
            unset($lock);
        }
    }

    public static function pushQueue(User $user): void
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

    /**
     * @inheritdoc
     */
    public function getTtr()
    {
        return 365 * 86400;
    }

    /**
     * @inheritdoc
     */
    public function canRetry($attempt, $error)
    {
        return $attempt < 10;
    }
}
