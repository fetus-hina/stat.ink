<?php
/**
 * @copyright Copyright (C) 2015-2019 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\jobs;

use Yii;
use app\models\User;
use app\models\UserStat2;
use yii\base\BaseObject;
use yii\queue\JobInterface;

class UserStatsJob extends BaseObject implements JobInterface
{
    public $user;
    public $version;

    public function execute($queue)
    {
        if (!$user = User::findOne(['id' => (int)$this->user])) {
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

            default:
                break;
        }
    }
}
