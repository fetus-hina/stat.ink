<?php
/**
 * @copyright Copyright (C) 2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\assets;

use yii\web\AssetBundle;

class BootstrapNotifyAsset extends AssetBundle
{
    public $sourcePath = '@bower/remarkable-bootstrap-notify';
    public $js = [
        'bootstrap-notify.min.js',
    ];
    public $depends = [
        \yii\bootstrap\BootstrapAsset::class,
    ];
}
