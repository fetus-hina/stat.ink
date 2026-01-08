<?php

/**
 * @copyright Copyright (C) 2019-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\assets;

use yii\web\AssetBundle;

class PhotoSwipeSimplifyAsset extends AssetBundle
{
    public $sourcePath = '@node/photoswipe-simplify/dist/js';
    public $js = [
        'photoswipe-simplify.min.js',
    ];
    public $depends = [
        PhotoSwipeAsset::class,
    ];

    public function registerAssetFiles($view)
    {
        parent::registerAssetFiles($view);
        $view->registerJs('photoswipeSimplify.init();');
    }
}
