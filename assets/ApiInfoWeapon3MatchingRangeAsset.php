<?php

/**
 * @copyright Copyright (C) 2019-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\assets;

use jp3cki\yii2\jqueryColor\JqueryColorAsset;
use yii\web\AssetBundle;
use yii\web\JqueryAsset;

final class ApiInfoWeapon3MatchingRangeAsset extends AssetBundle
{
    public $sourcePath = '@app/resources/.compiled/stat.ink';

    public $js = [
        'api-info-weapon3-matching-range.js',
    ];

    public $depends = [
        JqueryAsset::class,
        JqueryColorAsset::class,
    ];
}
