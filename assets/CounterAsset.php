<?php
/**
 * @copyright Copyright (C) 2015-2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\assets;

use yii\web\AssetBundle;

class CounterAsset extends AssetBundle
{
    public $sourcePath = '@app/resources/.compiled/counter';
    public $css = [
        'counter.css',
    ];
    public $js = [
        'counter.js',
    ];
    public $depends = [
        DsegFontAsset::class,
        'yii\web\JqueryAsset',
    ];
}
