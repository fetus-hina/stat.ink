<?php
/**
 * @copyright Copyright (C) 2015-2019 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

declare(strict_types=1);

namespace app\assets;

use yii\web\AssetBundle;

class PhotoSwipeAsset extends AssetBundle
{
    public $sourcePath = '@npm/photoswipe/dist';
    public $js = [
        'photoswipe.min.js',
        'photoswipe-ui-default.min.js',
    ];
    public $css = [
        'photoswipe.css',
        'default-skin/default-skin.css',
    ];
}
