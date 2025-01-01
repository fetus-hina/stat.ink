<?php

/**
 * @copyright Copyright (C) 2018-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\assets;

use yii\web\AssetBundle;
use yii\web\JqueryAsset;

class OsIconWidgetAsset extends AssetBundle
{
    public $sourcePath = '@app/resources/.compiled/stat.ink';
    public $js = [
        'os-icon-widget.js',
    ];
    public $depends = [
        BowserAsset::class,
        JqueryAsset::class,
    ];
}
