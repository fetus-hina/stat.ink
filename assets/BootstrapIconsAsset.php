<?php

/**
 * @copyright Copyright (C) 2022-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\assets;

use yii\web\AssetBundle;

final class BootstrapIconsAsset extends AssetBundle
{
    public $sourcePath = '@app/vendor/twbs/bootstrap-icons/font';
    public $css = [
        'bootstrap-icons.css',
    ];
}
