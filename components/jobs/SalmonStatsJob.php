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
use app\components\helpers\SalmonStatsV3;
use app\models\User;
use app\models\UserStatSalmon3;
use yii\base\BaseObject;
use yii\queue\JobInterface;

final class SalmonStatsJob extends BaseObject implements JobInterface
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
            case 2:
                break;

            case 3:
                $lock = CriticalSection::lock(
                    \hash_hmac('sha256', (string)$user->id, UserStatSalmon3::class),
                    60,
                    Yii::$app->pgMutex,
                );
                try {
                    SalmonStatsV3::create($user);
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
