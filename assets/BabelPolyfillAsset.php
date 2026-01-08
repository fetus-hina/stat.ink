<?php

/**
 * @copyright Copyright (C) 2017-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\assets;

use yii\web\AssetBundle;

class BabelPolyfillAsset extends AssetBundle
{
    public $sourcePath = '@node/@babel/polyfill/dist';
    public $js = [
        'polyfill.min.js',
    ];
}
