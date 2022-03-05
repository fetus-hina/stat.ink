<?php

/**
 * @copyright Copyright (C) 2015-2019 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\jobs;

use Yii;
use yii\base\BaseObject;
use yii\queue\JobInterface;

class ImageS3Job extends BaseObject implements JobInterface
{
    use JobPriority;

    public $file;

    public function execute($queue)
    {
        $s3 = Yii::$app->imgS3;
        if (!$s3 || !$s3->enabled) {
            return;
        }

        $file = (string)$this->file;
        if (!preg_match('/\b([a-z2-9]{26}\.jpg)$/', $file, $match)) {
            return;
        }

        $file = $match[1];
        $path = implode('/', [
            Yii::getAlias('@app/web/images'),
            substr($file, 0, 2),
            $file,
        ]);
        for ($retry = 0; $retry < 3; ++$retry) {
            if (!@file_exists($path)) {
                return;
            }
            try {
                $ret = $s3->uploadFile(
                    $path,
                    implode('/', [
                        substr($file, 0, 2),
                        $file,
                    ])
                );
                if ($ret) {
                    @unlink($path);
                    return;
                }
            } catch (\Throwable $e) {
            }
        }
    }
}
