<?php

/**
 * @copyright Copyright (C) 2019-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

namespace app\components\jobs;

use Yii;
use yii\base\BaseObject;
use yii\helpers\FileHelper;
use yii\queue\JobInterface;

use function dirname;
use function escapeshellarg;
use function exec;
use function file_exists;
use function unlink;
use function vsprintf;

class ImageOptimizeJob extends BaseObject implements JobInterface
{
    use JobPriority;

    public ?string $inPath = null;
    public ?string $outPath = null;

    public function execute($queue)
    {
        $inPath = $this->inPath;
        $outPath = $this->outPath;
        if ($inPath === null || $outPath === null) {
            Yii::error('The input or output path is not set.', __METHOD__);
            return;
        }

        if (!@file_exists($inPath)) {
            Yii::error("File {$inPath} does not exist.", __METHOD__);
            return;
        }

        if (@file_exists($outPath)) {
            Yii::error("File {$outPath} already exists.", __METHOD__);
            return;
        }

        if (!FileHelper::createDirectory(dirname($outPath))) {
            Yii::error('Could not create directory ' . dirname($outPath), __METHOD__);
            return;
        }

        $cmdline = vsprintf('/usr/bin/env optipng --quiet -strip all -o7 -out %s %s', [
            escapeshellarg($outPath),
            escapeshellarg($inPath),
        ]);
        $lines = null;
        $status = null;
        @exec($cmdline, $lines, $status);
        if ($status != 0) {
            Yii::error('Optimize failed', __METHOD__);
            return;
        }
        @unlink($inPath);
    }
}
