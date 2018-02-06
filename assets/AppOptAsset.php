<?php
/**
 * @copyright Copyright (C) 2015-2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\assets;

use Yii;
use yii\helpers\ArrayHelper;
use yii\web\AssetBundle;

class AppOptAsset extends AssetBundle
{
    public $sourcePath = '@app/resources/.compiled/stat.ink';
    public $depends = [
        AppAsset::class,
    ];

    public function registerCssFile($view, $filename, $options = [], $key = null)
    {
        $view->registerCssFile(
            Yii::$app->assetManager->getAssetUrl($this, $filename),
            $options,
            $key
        );
    }

    public function registerJsFile($view, $filename, $options = [], $key = null)
    {
        $view->registerJsFile(
            Yii::$app->assetManager->getAssetUrl($this, $filename),
            ArrayHelper::merge(
                ['depends' => [
                    static::class,
                ]],
                $options
            ),
            $key
        );
    }
}
