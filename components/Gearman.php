<?php
/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\components;

use shakura\yii2\gearman\GearmanComponent;

class Gearman extends GearmanComponent
{
    public function init()
    {
        parent::init();
        $jobs = [];
        foreach ($this->jobs as $name => $job) {
            if (is_int($name)) {
                if (is_array($job) &&
                    isset($job['class']) &&
                    is_callable([$job['class'], 'jobName'])
                ) {
                    $name = call_user_func([$job['class'], 'jobName']);
                    $jobs[$name] = $job;
                } elseif (is_string($job) && is_callable([$job, 'jobName'])) {
                    $name = call_user_func([$job, 'jobName']);
                    $jobs[$name] = ['class' => $job];
                } else {
                    $jobs[$name] = $job;
                }
            } else {
                $jobs[$name] = $job;
            }
        }
        $this->jobs = $jobs;
    }
}
