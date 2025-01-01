<?php

/**
 * @copyright Copyright (C) 2021-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\assets;

use yii\web\AssetBundle;

final class ReactCounterAppAsset extends AssetBundle
{
    public $sourcePath = '@app/resources/.compiled/react';
    public $js = [
        'counter-app.js',
    ];
    public $depends = [
        DsegAsset::class,
    ];
}
