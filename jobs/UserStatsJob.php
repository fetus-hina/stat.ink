<?php
/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\jobs;

use GearmanJob;
use Yii;
use app\models\User;
use app\models\UserStat2;
use shakura\yii2\gearman\JobWorkload;

class UserStatsJob extends BaseJob
{
    public static function jobName() : string
    {
        return sprintf(
            'statinkUpdateUserStats_%s',
            substr(hash('sha256', __DIR__), 0, 8)
        );
    }

    public function job(JobWorkload $workload, GearmanJob $job)
    {
        $params = $workload->getParams();
        if (!$user = User::findOne(['id' => $params['user'] ?? -1])) {
            return;
        }
        switch ($params['version'] ?? -1) {
            case 1:
                break;

            case 2:
                $stats = $user->userStat2 ?? Yii::createObject([
                    'class' => UserStat2::class,
                    'user_id' => $user->id,
                ]);
                $stats->makeUpdate()->update();
                break;

            default:
                break;
        }
    }
}
