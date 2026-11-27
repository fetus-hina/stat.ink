<?php

/**
 * @copyright Copyright (C) 2023-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

namespace app\components\jobs;

use Override;
use Yii;
use app\components\helpers\CriticalSection;
use app\components\helpers\UserExportJson3Helper;
use app\models\User;
use yii\base\BaseObject;
use yii\queue\RetryableJobInterface;

final class UserExportJson3Job extends BaseObject implements RetryableJobInterface
{
    use JobPriority;

    private const LOCK_TIMEOUT_SEC = 180;

    public int $user;

    /**
     * @inheritdoc
     */
    #[Override]
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
            UserExportJson3Helper::lockName($userModel),
            self::LOCK_TIMEOUT_SEC,
            Yii::$app->pgMutex,
        );
        try {
            UserExportJson3Helper::update($userModel);
        } finally {
            unset($lock);
        }
    }

    public static function pushQueue(User $user): void
    {
        Yii::$app->queue
            ->priority(self::getJobPriority())
            ->push(
                new self([
                    'user' => $user->id,
                ]),
            );
    }

    /**
     * @inheritdoc
     *
     * A job reserved longer than this is released and retried, e.g. when the worker
     * is restarted on deployment. The export saves its progress every 200 battles,
     * so a retried job continues from where it stopped.
     */
    #[Override]
    public function getTtr(): int
    {
        return 3600;
    }

    /**
     * @inheritdoc
     */
    #[Override]
    public function canRetry($attempt, $error): bool
    {
        return $attempt < 10;
    }
}
