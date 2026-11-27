<?php

/**
 * @copyright Copyright (C) 2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

namespace app\assets\zxcvbn;

use yii\web\AssetBundle;

/**
 * Base class of the zxcvbn-ts asset bundles
 *
 * Every zxcvbn-ts package ships a browser-ready IIFE bundle as dist/zxcvbn-ts.js,
 * which registers itself to window.zxcvbnts.
 */
abstract class ZxcvbnAsset extends AssetBundle
{
    public $js = [
        'zxcvbn-ts.js',
    ];

    public $publishOptions = [
        'only' => [
            'zxcvbn-ts.js',
            'zxcvbn-ts.js.map',
        ],
    ];
}
