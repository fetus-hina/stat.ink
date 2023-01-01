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
use yii\helpers\FileHelper;
use yii\queue\JobInterface;

class ImageOptimizeJob extends BaseObject implements JobInterface
{
    use JobPriority;

    public $inPath;
    public $outPath;

    public function execute($queue)
    {
        if (!@file_exists($this->inPath)) {
            Yii::error("File {$this->inPath} does not exist.", __METHOD__);
            return;
        }

        if (@file_exists($this->outPath)) {
            Yii::error("File {$this->outPath} already exists.", __METHOD__);
            return;
        }

        if (!FileHelper::createDirectory(dirname($this->outPath))) {
            Yii::error('Could not create directory ' . dirname($this->outPath), __METHOD__);
            return;
        }

        $cmdline = vsprintf('/usr/bin/env %s -quiet -strip all -o7 -out %s %s', [
            escapeshellarg(Yii::getAlias('@app/node_modules/.bin/optipng')),
            escapeshellarg($this->outPath),
            escapeshellarg($this->inPath),
        ]);
        $lines = null;
        $status = null;
        @exec($cmdline, $lines, $status);
        if ($status != 0) {
            Yii::error('Optimize failed', __METHOD__);
            return;
        }
        @unlink($this->inPath);
    }
}
