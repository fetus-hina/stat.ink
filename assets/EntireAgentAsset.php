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
use jp3cki\yii2\flot\FlotTimeAsset;
use yii\helpers\ArrayHelper;
use yii\web\AssetBundle;
use yii\web\JqueryAsset;

class EntireAgentAsset extends AssetBundle
{
    public $sourcePath = '@app/resources/.compiled/stat.ink';
    public $js = [
        'agent.js',
    ];
    public $depends = [
        ColorSchemeAsset::class,
        FlotAsset::class,
        FlotTimeAsset::class,
        JqueryAsset::class,
    ];
}
