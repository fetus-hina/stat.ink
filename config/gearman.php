<?php
return [
    'class' => 'app\components\Gearman',
    'servers' => [
        ['host' => '127.0.0.1', 'port' => 4730],
    ],
    'jobs' => [
        'app\jobs\ImageOptimizeJob',
        'app\jobs\ImageS3Job',
        'app\jobs\UserStatsJob',
        'app\jobs\battle\OstatusJob',
        'app\jobs\battle\SlackJob',
    ],
];
