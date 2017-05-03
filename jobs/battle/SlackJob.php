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
use app\models\Slack;
use shakura\yii2\gearman\JobWorkload;

class SlackJob extends BaseJob
{
    static public function jobName() : string
    {
        return sprintf(
            'statinkPushBattleToSlack_%s',
            substr(hash('sha256', __DIR__), 0, 8)
        );
    }

    public function battle1Job(Battle $battle, JobWorkload $workload, GearmanJob $job)
    {
        $query = Slack::find()
            ->with('user')
            ->andWhere([
                'user_id' => $battle->user_id,
                'suspended' => false,
            ])
            ->orderBy('id');
        foreach ($query->each() as $slack) {
            try {
                $slack->send($battle);
            } catch (\Exception $e) {
            }
        }
    }

    public function battle2Job(Battle2 $battle, JobWorkload $workload, GearmanJob $job)
    {
        //FIXME
    }
}
