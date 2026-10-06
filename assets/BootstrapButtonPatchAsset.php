<?php

/**
 * @copyright Copyright (C) 2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

namespace app\assets;

use yii\bootstrap\BootstrapPluginAsset;
use yii\web\AssetBundle;
use yii\web\JqueryAsset;

/**
 * Mitigation for CVE-2024-6485 (Bootstrap 3 button plugin XSS)
 */
class BootstrapButtonPatchAsset extends AssetBundle
{
    public $sourcePath = '@app/resources/.compiled/stat.ink';
    public $js = [
        'bootstrap-button-patch.js',
    ];
    public $depends = [
        BootstrapPluginAsset::class,
        JqueryAsset::class,
    ];
}
