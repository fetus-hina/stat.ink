<?php

/**
 * @copyright Copyright (C) 2024-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\jobs;

use Yii;
use app\components\helpers\UserPlayedWith3Helper;
use app\models\Salmon3;
use app\models\User;
use yii\base\BaseObject;
use yii\queue\JobInterface;

final class SalmonPlayedWith3Job extends BaseObject implements JobInterface
{
    use JobPriority;

    public int $user = 0;
    public int|null $id = null;

    /**
     * @inheritdoc
     */
    public function execute($queue)
    {
        $user = User::find()
            ->andWhere(['id' => (int)$this->user])
            ->limit(1)
            ->one();
        if (!$user) {
            return;
        }

        if ($this->id === null) {
            UserPlayedWith3Helper::rebuildUserSalmon($user);
            return;
        }

        $job = Salmon3::find()
            ->andWhere([
                'user_id' => $user->id,
                'id' => $this->id,
                'is_deleted' => false,
            ])
            ->limit(1)
            ->one();
        if (!$job) {
            return;
        }

        UserPlayedWith3Helper::updateSalmon($user, $job);
    }

    public static function pushQueue(User $user, ?Salmon3 $job): void
    {
        Yii::$app->queue
            ->priority(self::getJobPriority())
            ->push(new self([
                'user' => $user->id,
                'id' => $job?->id ?? null,
            ]));
    }
}
