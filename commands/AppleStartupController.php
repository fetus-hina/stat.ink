<?php
/**
 * @copyright Copyright (C) 2015 AIZAWA Hina
 * @license https://github.com/fetus-hina/fest.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\commands;

use Yii;
use yii\console\Controller;
use yii\helpers\Console;

class AppleStartupController extends Controller
{
    public $ttf;

    public function options($actionID)
    {
        return array_merge(
            parent::options($actionID),
            ['ttf']
        );
    }

    public function actionCreate($outPath)
    {
        $this->stdout("Generating startup screen image {$outPath} ...\n", Console::FG_YELLOW);
        if (!$options = $this->getCreateOptionFromFilename($outPath)) {
            $this->stdout("    Failed to guess options from filename\n", Console::FG_RED);
            return 1;
        }
        $this->stdout("    Rotation: {$options->rotation}\n", Console::FG_BLUE);
        $this->stdout("    Device Width: {$options->deviceWidth}\n", Console::FG_BLUE);
        $this->stdout("    Device Height: {$options->deviceHeight}\n", Console::FG_BLUE);

        $image = imagecreatetruecolor($options->imageWidth, $options->imageHeight);
        imagefill($image, 0, 0, 0xff9693);
    
        // calc font size
        $textTargetWidth = min($options->imageWidth * 0.75, 400 * $options->pixelRatio);
        for ($fontSize = 64 * $options->pixelRatio; $fontSize > 1; --$fontSize) {
            $bbox = imagettfbbox($fontSize, 0, $this->ttf, Yii::$app->name);
            $bboxWidth = $bbox[2] - $bbox[0];
            $bboxHeight = $bbox[1] - $bbox[5];
            if ($bboxWidth <= $textTargetWidth) {
                break;
            }
        }

        $goldenRatio = (1 + sqrt(5)) / 2;
        $textX = $options->imageWidth / 2 - $bboxWidth / 2 + $bbox[0];
        $textY = $options->imageHeight / 2 - $bboxHeight / 2 - $bbox[5];
        imagettftext($image, $fontSize, 0, $textX, $textY, 0xffffff, $this->ttf, Yii::$app->name);

        if ($options->rotation === 'landscape') {
            imagerotate($image, 90, 0x000000);
        }

        if (!imagepng($image, $outPath, 9, PNG_ALL_FILTERS)) {
            $this->stdout("Failed to save PNG image.\n", Console::FG_RED);
            return 1;
        }
    
        $this->stdout("Done.\n", Console::FG_GREEN);
        return 0;
    }

    private function getCreateOptionFromFilename($path)
    {
        if (!preg_match('/^([lp])-(\d+)x(\d+)@(\d+)x\.png$/', basename($path), $match)) {
            return false;
        }
        $ret = (object)[
            'rotation'      => $match[1] === 'l' ? 'landscape' : 'portrait',
            'cssWidth'      => (int)$match[2],
            'cssHeight'     => (int)$match[3],
            'deviceWidth'   => (int)$match[2] * (int)$match[4],
            'deviceHeight'  => (int)$match[3] * (int)$match[4],
            'pixelRatio'    => (int)$match[4],
            'imageWidth'    => null,
            'imageHeight'   => null,
        ];
        if ($ret->rotation === 'portrait') {
            $ret->imageWidth = $ret->deviceWidth;
            $ret->imageHeight = $ret->deviceHeight - (20 * (int)$match[4]);
        } else {
            $ret->imageWidth = $ret->deviceHeight;
            $ret->imageHeight = $ret->deviceWidth - (20 * (int)$match[4]);
        }
        return $ret;
    }
}
