<?php
/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\assets;

use statink\yii2\sillyname\SillynameAsset;
use yii\web\AssetBundle;
use yii\web\JqueryAsset;

class NameAnonymizerAsset extends AssetBundle
{
    public $sourcePath = '@app/resources/.compiled/stat.ink';
    public $js = [
        'name-anonymizer.js',
    ];
    public $css = [
        'name-anonymizer.css',
    ];
    public $depends = [
        JqueryAsset::class,
        SillyNameAsset::class,
    ];
}
