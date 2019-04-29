<?php
/**
 * @copyright Copyright (C) 2015-2019 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

declare(strict_types=1);

namespace app\assets;

use Yii;
use jp3cki\yii2\flot\FlotAsset;
use jp3cki\yii2\flot\FlotPieAsset;
use yii\helpers\ArrayHelper;
use yii\web\AssetBundle;
use yii\web\JqueryAsset;

class EntireKnockoutAsset extends AssetBundle
{
    public $sourcePath = '@app/resources/.compiled/stat.ink';
    public $js = [
        'knockout.js',
    ];
    public $depends = [
        ColorSchemeAsset::class,
        FlotAsset::class,
        FlotPieAsset::class,
        JqueryAsset::class,
    ];
}
