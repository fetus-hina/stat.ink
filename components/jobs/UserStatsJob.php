<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\jobs;

use Yii;
use app\components\helpers\CriticalSection;
use app\components\helpers\UserStatsV3;
use app\models\User;
use app\models\UserStat2;
use app\models\UserStat3;
use yii\base\BaseObject;
use yii\queue\JobInterface;

use function hash_hmac;

final class UserStatsJob extends BaseObject implements JobInterface
{
    use JobPriority;

    public $user;
    public $version;

    public function execute($queue)
    {
        $user = User::find()
            ->andWhere(['id' => (int)$this->user])
            ->limit(1)
            ->one();
        if (!$user) {
            return;
        }

        switch ($this->version) {
            case 1:
                break;

            case 2:
                $lock = null;
                try {
                    $lock = UserStat2::getLock($user->id, 10.0, false);
                    $stats = $user->userStat2 ?? Yii::createObject([
                        'class' => UserStat2::class,
                        'user_id' => $user->id,
                    ]);
                    $stats->makeUpdate()->save();
                } finally {
                    unset($lock);
                }
                break;

            case 3:
                $lock = CriticalSection::lock(
                    hash_hmac('sha256', (string)$user->id, UserStat3::class),
                    60,
                    Yii::$app->pgMutex,
                );
                try {
                    UserStatsV3::create($user);
                } finally {
                    unset($lock);
                }
                break;

            default:
                break;
        }
    }

    public static function pushQueue3(User $user): void
    {
        Yii::$app->queue
            ->priority(self::getJobPriority())
            ->push(new self([
                'version' => 3,
                'user' => $user->id,
            ]));
    }
}
