<?php
/**
 * @copyright Copyright (C) 2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\assets;

use yii\web\AssetBundle;

class EmojifyJsAsset extends AssetBundle
{
    public $sourcePath = '@bower/emojify.js/dist';
    public $js = [
        'js/emojify.min.js',
    ];
    public $css = [
        'css/basic/emojify.min.css',
        'css/data-uri/emojify.min.css',
    ];
}
