<?php
/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\assets;

use yii\web\AssetBundle;

class NpmSillyNameAsset extends AssetBundle
{
    // public $sourcePath = '@npm/sillyname';
    public $sourcePath = '@app/resources/.compiled/sillyname';
    public $js = [
        'sillyname.js',
    ];
    public $depends = [
    ];
}
