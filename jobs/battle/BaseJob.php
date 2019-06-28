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
use shakura\yii2\gearman\JobWorkload;

abstract class BaseJob extends \app\jobs\BaseJob
{
    abstract public function battle1Job(Battle $battle, JobWorkload $workload, GearmanJob $job);
    abstract public function battle2Job(Battle2 $battle, JobWorkload $workload, GearmanJob $job);

    public function job(JobWorkload $workload, GearmanJob $job)
    {
        $params = $workload->getParams();
        if (isset($params['hostInfo'])) {
            $urlManager = Yii::$app->getUrlManager();
            $urlManager->baseUrl = $params['hostInfo'];
            $urlManager->hostInfo = $params['hostInfo'];
        }

        switch ($params['version'] ?? 'unspecified') {
            case 1:
                if ($battle = Battle::findOne(['id' => $params['battle']])) {
                    $this->battle1Job($battle, $workload, $job);
                }
                break;

            case 2:
                if ($battle = Battle2::findOne(['id' => $params['battle']])) {
                    $this->battle2Job($battle, $workload, $job);
                }
                break;

            default:
                Yii::error(__METHOD__ . '(): unsupported battle version ' . $params['version']);
                break;
        }
    }
}
