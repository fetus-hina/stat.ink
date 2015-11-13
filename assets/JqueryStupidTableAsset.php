<?php
/**
 * @copyright Copyright (C) 2015 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\assets;

use yii\web\AssetBundle;

class JqueryStupidTableAsset extends AssetBundle
{
    public $sourcePath = '@bower/jquery-stupid-table';
    public $js = [
        'stupidtable.min.js',
    ];
    public $depends = [
        'yii\web\JqueryAsset',
    ];
}
