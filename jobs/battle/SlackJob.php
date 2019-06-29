<?php
/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\jobs\battle;

use GearmanJob;
use Yii;
use app\models\Battle2;
use app\models\Battle;
use app\models\Slack;
use app\models\User;
use shakura\yii2\gearman\JobWorkload;

class SlackJob extends BaseJob
{
    public static function jobName() : string
    {
        return sprintf(
            'statinkPushBattleToSlack_%s',
            substr(hash('sha256', __DIR__), 0, 8)
        );
    }

    private function querySlackTask(User $user)
    {
        $query = Slack::find()
            ->with('user')
            ->andWhere([
                'user_id' => $user->id,
                'suspended' => false,
            ])
            ->orderBy('id');
        foreach ($query->each() as $slack) {
            yield $slack;
        }
    }

    public function battle1Job(Battle $battle, JobWorkload $workload, GearmanJob $job)
    {
        foreach ($this->querySlackTask($battle->user) as $slack) {
            try {
                $slack->send($battle);
            } catch (\Exception $e) {
            }
        }
    }

    public function battle2Job(Battle2 $battle, JobWorkload $workload, GearmanJob $job)
    {
        foreach ($this->querySlackTask($battle->user) as $slack) {
            try {
                $slack->send($battle);
            } catch (\Exception $e) {
            }
        }
    }
}
