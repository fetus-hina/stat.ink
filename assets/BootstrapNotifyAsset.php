<?php

/**
 * @copyright Copyright (C) 2016-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\assets;

use yii\bootstrap\BootstrapAsset;
use yii\web\AssetBundle;

class BootstrapNotifyAsset extends AssetBundle
{
    public $sourcePath = '@node/bootstrap-notify';
    public $js = [
        'bootstrap-notify.min.js',
    ];
    public $depends = [
        BootstrapAsset::class,
    ];
}
