<?php
/**
 * @copyright Copyright (C) 2015 AIZAWA Hina
 * @license https://github.com/fetus-hina/fest.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\assets;

use yii\web\AssetBundle;

class TwitterWidgetAsset extends AssetBundle
{
    public $baseUrl = '//platform.twitter.com/';
    public $js = [
        'widgets.js',
    ];
    public $jsOptions = [
        'async' => 'async',
    ];
}
