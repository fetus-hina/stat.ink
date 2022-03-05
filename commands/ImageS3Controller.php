<?php

/**
 * @copyright Copyright (C) 2015-2019 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\commands;

use FilterIterator;
use Yii;
use app\components\ImageS3;
use app\components\jobs\ImageS3Job;
use yii\console\Controller;
use yii\helpers\Console;
use yii\helpers\FileHelper;

class ImageS3Controller extends Controller
{
    public const AUTOUPLOAD_DELAY = 7 * 86400;

    public function init()
    {
        parent::init();
        Yii::setAlias('@image', Yii::getAlias('@app/web/images'));
    }

    public function actionUpload(string $path, bool $queue = false): int
    {
        // {{{
        if (!Yii::$app->imgS3->enabled) {
            $this->stderr(
                "The component \"imgS3\" is not enabled.\n",
                Console::FG_RED
            );
            return false;
        }

        if (!preg_match('/\b([a-z2-9]{26}\.jpg)$/', $path, $match)) {
            $this->stderr(
                "The specified path {$path} is not a valid file name of image.\n",
                Console::BOLD,
                Console::FG_RED
            );
            return 2;
        }
        $path = implode('/', [Yii::getAlias('@image'), substr($match[1], 0, 2), $match[1]]);
        if (!@file_exists($path)) {
            $this->stderr(
                "File does not exist: {$path}\n",
                Console::BOLD,
                Console::FG_RED
            );
            return 2;
        }

        if (!$queue) {
            $this->stderr(sprintf(
                "%s file %s to S3 storage.\n",
                Console::ansiFormat("Uploading", [Console::BOLD, Console::FG_GREEN]),
                Console::ansiFormat(basename($path), [Console::BOLD, Console::FG_PURPLE])
            ));
            $ret = Yii::$app->imgS3->uploadFile(
                $path,
                implode('/', [
                    substr(basename($path), 0, 2),
                    basename($path),
                ])
            );
            if (!$ret) {
                $this->stderr(
                    "Failed to upload file.\n",
                    Console::BOLD,
                    Console::FG_RED
                );
                return 1;
            }
            $this->stderr("SUCCESS!\n", Console::BOLD, Console::FG_GREEN);
        } else {
            Yii::$app->queue
                ->priority(ImageS3Job::getJobPriority())
                ->push(new ImageS3Job([
                    'file' => $path,
                ]));
            $this->stderr(sprintf(
                "%s: %s\n",
                Console::ansiFormat("Queued", [Console::BOLD, Console::FG_GREEN]),
                Console::ansiFormat(basename($path), [Console::BOLD, Console::FG_PURPLE])
            ));
        }
        return 0;
        // }}}
    }

    public function actionAutoUpload(): int
    {
        // {{{
        if (!$lock = $this->lockAutoUpload()) {
            return 1;
        }

        $innerIterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator(Yii::getAlias('@image'))
        );
        $iterator = new class ($innerIterator) extends FilterIterator {
            public function accept()
            {
                $entry = $this->getInnerIterator()->current();
                return (
                    $entry->isFile() &&
                    preg_match('/^[a-z2-9]{26}\.jpg$/', $entry->getBasename()) &&
                    time() - $entry->getMTime() >= ImageS3Controller::AUTOUPLOAD_DELAY
                );
            }
        };
        foreach ($iterator as $entry) {
            $status = $this->actionUpload($entry->getBasename(), true);
        }
        //}}}
        return 0;
    }

    private function lockAutoUpload()
    {
        $path = Yii::getAlias('@app/runtime/images3-auto-upload.lock');
        if (!file_exists(dirname($path))) {
            FileHelper::createDirectory(dirname($path));
        }
        if (!$lock = @fopen($path, 'c+')) {
            $this->stderr("Could not open lock file: {$path}\n", Console::FG_RED, Console::BOLD);
            return false;
        }
        if (!flock($lock, LOCK_EX | LOCK_NB)) {
            $this->stderr(
                "Could not get file lock. Another process running?\n",
                Console::FG_RED,
                Console::BOLD
            );
            return false;
        }
        return $lock;
    }
}
