<?php
/**
 * @copyright Copyright (C) 2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\assets\gears;

use yii\web\AssetBundle;

class RespawnAsset extends AssetBundle
{
    public $sourcePath = '@app/resources/gears';
    public $js = [
        'respawn.js',
    ];
}
