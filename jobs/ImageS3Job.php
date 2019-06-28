<?php
/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\jobs;

use GearmanJob;
use Yii;
use shakura\yii2\gearman\JobWorkload;

class ImageS3Job extends BaseJob
{
    public static function jobName() : string
    {
        return sprintf(
            'statinkImageS3_%s',
            substr(hash('sha256', __DIR__), 0, 8)
        );
    }

    public function job(JobWorkload $workload, GearmanJob $job)
    {
        if (!Yii::$app->imgS3->enabled) {
            return;
        }

        $params = $workload->getParams();
        $file = $params['file'];
        if (!preg_match('/\b([a-z2-9]{26}\.jpg)$/', $file, $match)) {
            return;
        }
        $file = $match[1];
        $path = implode('/', [
            Yii::getAlias('@app/web/images'),
            substr($file, 0, 2),
            $file
        ]);
        for ($retry = 0; $retry < 3; ++$retry) {
            if (!@file_exists($path)) {
                return;
            }
            try {
                $ret = Yii::$app->imgS3->uploadFile(
                    $path,
                    implode('/', [
                        substr($file, 0, 2),
                        $file
                    ])
                );
                if ($ret) {
                    @unlink($path);
                    return;
                }
            } catch (\Exception $e) {
            }
        }
    }
}
