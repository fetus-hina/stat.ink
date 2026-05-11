<?php

/**
 * @copyright Copyright (C) 2019-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

namespace app\assets;

use yii\bootstrap\BootstrapAsset;
use yii\web\AssetBundle;
use yii\web\JqueryAsset;

class TimezoneDialogAsset extends AssetBundle
{
    public $sourcePath = '@app/resources/.compiled/stat.ink';
    public $js = [
        'timezone-dialog.js',
    ];
    public $depends = [
        ApiFetchAsset::class,
        BootstrapAsset::class,
        FontAwesomeAsset::class,
        JqueryAsset::class,
    ];
}
