<?php

/**
 * @copyright Copyright (C) 2024-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\jobs;

use Yii;
use app\components\helpers\UserPlayedWith3Helper;
use app\models\Battle3;
use app\models\User;
use yii\base\BaseObject;
use yii\queue\JobInterface;

final class BattlePlayedWith3Job extends BaseObject implements JobInterface
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
            UserPlayedWith3Helper::rebuildUserBattle($user);
            return;
        }

        $battle = Battle3::find()
            ->andWhere([
                'user_id' => $user->id,
                'id' => $this->id,
                'is_deleted' => false,
            ])
            ->limit(1)
            ->one();
        if (!$battle) {
            return;
        }

        UserPlayedWith3Helper::updateBattle($user, $battle);
    }

    public static function pushQueue(User $user, ?Battle3 $battle): void
    {
        Yii::$app->queue
            ->priority(self::getJobPriority())
            ->push(new self([
                'user' => $user->id,
                'id' => $battle?->id ?? null,
            ]));
    }
}
