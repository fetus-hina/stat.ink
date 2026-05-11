<?php

/**
 * @copyright Copyright (C) 2016-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

namespace app\assets;

use yii\web\AssetBundle;
use yii\web\JqueryAsset;

class SlackAsset extends AssetBundle
{
    public $sourcePath = '@app/resources/.compiled/slack';
    public $js = [
        'slack.js',
    ];
    public $depends = [
        ApiFetchAsset::class,
        BootstrapToggleAsset::class,
        JqueryAsset::class,
    ];
}
