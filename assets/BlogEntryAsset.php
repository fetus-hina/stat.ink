<?php

/**
 * @copyright Copyright (C) 2019-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\assets;

use yii\bootstrap\BootstrapAsset;
use yii\web\AssetBundle;

class BlogEntryAsset extends AssetBundle
{
    public $sourcePath = '@app/resources/.compiled/stat.ink';
    public $css = [
        'blog-entries.css',
    ];
    public $depends = [
        AppAsset::class,
        BootstrapAsset::class,
    ];
}
