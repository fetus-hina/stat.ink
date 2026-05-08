<?php

/**
 * @copyright Copyright (C) 2016-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

namespace app\assets;

use yii\web\AssetBundle;

class ActiveReltimeAsset extends AssetBundle
{
    public $sourcePath = '@app/resources/.compiled/stat.ink';
    public $js = [
        'active-reltime.js',
    ];
    public $depends = [
        'yii\web\JqueryAsset',
    ];
}
