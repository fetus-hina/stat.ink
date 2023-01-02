<?php

/**
 * @copyright Copyright (C) 2015-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\jobs;

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
