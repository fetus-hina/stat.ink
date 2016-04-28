<?php
/**
 * @copyright Copyright (C) 2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\assets;

use yii\web\AssetBundle;

class EmojifyAsset extends AssetBundle
{
    public $sourcePath = '@app/resources/.compiled/emoji';
    public $js = [
        'emoji.js',
    ];
    public $depends = [
        'yii\web\JqueryAsset',
        __NAMESPACE__ . '\EmojifyJsAsset',
    ];
}
