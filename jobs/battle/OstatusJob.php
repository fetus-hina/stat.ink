<?php
/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\jobs\battle;

use GearmanJob;
use Yii;
use app\models\Battle2;
use app\models\Battle;
use app\models\OstatusPubsubhubbub;
use shakura\yii2\gearman\JobWorkload;

class OstatusJob extends BaseJob
{
    static public function jobName() : string
    {
        return sprintf(
            'statinkPushBattleOstatus_%s',
            substr(hash('sha256', __DIR__), 0, 8)
        );
    }

    public function battle1Job(Battle $battle, JobWorkload $workload, GearmanJob $job)
    {
        $query = OstatusPubsubhubbub::find()
            ->active()
            ->andWhere(['topic' => $battle->user_id])
            ->orderBy('id');
        foreach ($query->each() as $hub) {
            try {
                $hub->notify($battle);
            } catch (\Exception $e) {
            }
        }
    }

    public function battle2Job(Battle2 $battle, JobWorkload $workload, GearmanJob $job)
    {
        //FIXME
    }
}
