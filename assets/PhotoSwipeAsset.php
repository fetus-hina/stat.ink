<?php

/**
 * @copyright Copyright (C) 2019-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\assets;

use yii\web\AssetBundle;

class PhotoSwipeAsset extends AssetBundle
{
    public $sourcePath = '@node/photoswipe/dist';
    public $js = [
        'photoswipe.min.js',
        'photoswipe-ui-default.min.js',
    ];
    public $css = [
        'photoswipe.css',
        'default-skin/default-skin.css',
    ];
}
