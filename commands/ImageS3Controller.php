<?php
/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\commands;

use Yii;
use app\components\ImageS3;
use yii\console\Controller;
use yii\helpers\Console;

class ImageS3Controller extends Controller
{
    public function init()
    {
        parent::init();
        Yii::setAlias('@image', Yii::getAlias('@app/web/images'));
    }

    public function actionUpload(string $path) : int
    {
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

        $this->stdout(sprintf(
            "%s file %s to S3 storage.\n",
            Console::ansiFormat("Uploading", [Console::BOLD, Console::FG_GREEN]),
            Console::ansiFormat(basename($path), [Console::BOLD, Console::FG_PURPLE])
        ));

        $ret = Yii::$app->imgS3->uploadFile(
            $path,
            implode('/', [
                substr(basename($path), 0, 2),
                basename($path)
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

        $this->stdout("SUCCESS!\n", Console::BOLD, Console::FG_GREEN);
        return 0;
    }
}
