<?php

/**
 * @copyright Copyright (C) 2019-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

namespace app\assets;

use yii\web\AssetBundle;

final class PhotoSwipeCoreAsset extends AssetBundle
{
    /**
     * @var string
     */
    public $sourcePath = '@node/photoswipe/dist';

    /**
     * @var list<string>
     */
    public $js = [
        'umd/photoswipe.umd.min.js',
        'umd/photoswipe-lightbox.umd.min.js',
    ];

    /**
     * @var list<string>
     */
    public $css = [
        'photoswipe.css',
    ];
}
