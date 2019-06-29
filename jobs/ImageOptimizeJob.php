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
use yii\helpers\FileHelper;

class ImageOptimizeJob extends BaseJob
{
    public static function jobName() : string
    {
        return sprintf(
            'statinkImageOptimize_%s',
            substr(hash('sha256', __DIR__), 0, 8)
        );
    }

    public function job(JobWorkload $workload, GearmanJob $job)
    {
        $params = $workload->getParams();
        $inPath = $params['inPath'];
        $outPath = $params['outPath'];

        if (!file_exists($inPath)) {
            Yii::error(__METHOD__ . '(): File does not exist. (' . $inPath . ')');
            return;
        }
        
        if (file_exists($outPath)) {
            Yii::error(__METHOD__ . '(): File already exist. (' . $outPath . ')');
            return;
        }

        if (!file_exists(dirname($outPath))) {
            FileHelper::createDirectory(dirname($outPath));
        }

        $cmdline = vsprintf('/usr/bin/env %s -quiet -strip all -o7 -out %s %s', [
            escapeshellarg(__DIR__ . '/../node_modules/.bin/optipng'),
            escapeshellarg($outPath),
            escapeshellarg($inPath),
        ]);
        $lines = null;
        $status = null;
        @exec($cmdline, $lines, $status);
        if ($status != 0) {
            Yii::error(__METHOD__ . '(): Optimize failed');
            return;
        }
        @unlink($inPath);
    }
}
