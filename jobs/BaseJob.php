<?php
/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\jobs;

use GearmanJob;
use shakura\yii2\gearman\JobBase;
use shakura\yii2\gearman\JobWorkload;

abstract class BaseJob extends JobBase
{
    abstract public function job(JobWorkload $workload, GearmanJob $job);

    public function execute(GearmanJob $job = null)
    {
        if ($job === null) {
            return;
        }
        if (!$workload = $this->getWorkload($job)) {
            return;
        }
        if (!$workload instanceof JobWorkload) {
            return;
        }
        $this->job($workload, $job);
    }
}
