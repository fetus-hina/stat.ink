<?php
/**
 * @copyright Copyright (C) 2015 AIZAWA Hina
 * @license https://github.com/fetus-hina/fest.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\assets;

use yii\web\AssetBundle;

class HeadingIkamodokiAsset extends AssetBundle
{
    public $sourcePath = '@app/resources/.compiled/heading-ikamodoki';
    public $js = [
        'heading-ikamodoki.js',
    ];
    public $jsOptions = [
        'async' => 'async',
    ];
    public $depends = [
        'yii\web\JqueryAsset',
        'app\assets\IkamodokiAsset',
    ];
}
