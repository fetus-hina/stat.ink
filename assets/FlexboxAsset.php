<?php

/**
 * @copyright Copyright (C) 2018-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\assets;

use yii\web\AssetBundle;

class FlexboxAsset extends AssetBundle
{
    public $sourcePath = '@app/resources/.compiled/flexbox';
    public $js = [
    ];
    public $css = [
        'flexbox.css',
    ];
    public $depends = [
        AppAsset::class,
    ];
}
