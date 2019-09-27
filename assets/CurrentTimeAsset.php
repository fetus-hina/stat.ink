<?php

/**
 * @copyright Copyright (C) 2015-2019 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\assets;

use statink\yii2\momentjs\MomentAsset;
use statink\yii2\momentjs\MomentTimezoneAsset;
use yii\web\AssetBundle;
use yii\web\JqueryAsset;

class CurrentTimeAsset extends AssetBundle
{
    public $sourcePath = '@app/resources/.compiled/stat.ink';
    public $js = [
        'current-time.js',
    ];
    public $depends = [
        JqueryAsset::class,
        MomentAsset::class,
        MomentTimezoneAsset::class,
    ];
}
