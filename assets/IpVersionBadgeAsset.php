<?php
/**
 * @copyright Copyright (C) 2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\assets;

use yii\web\AssetBundle;

class IpVersionBadgeAsset extends AssetBundle
{
    public $sourcePath = '@app/resources/.compiled/ip-version';
    public $css = [
        'https://fonts.googleapis.com/css?family=Audiowide',
        'badge.css',
    ];
    public $depends = [
        'yii\bootstrap\BootstrapAsset',
    ];
}
