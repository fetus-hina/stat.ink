<?php

/**
 * @copyright Copyright (C) 2015-2018 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\assets;

use Yii;
use app\components\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\web\AssetBundle;

class IrasutoyaAsset extends AssetBundle
{
    public $sourcePath = '@app/resources/.compiled/irasutoya';
    public $css = [];
    public $js = [];

    public function img(string $file, array $options = []): string
    {
        return Html::img(
            Yii::$app->assetManager->getAssetUrl($this, $file),
            ArrayHelper::merge([
                'style' => [
                    'height' => '1em',
                    'width' => 'auto',
                ],
            ], $options)
        );
    }
}
