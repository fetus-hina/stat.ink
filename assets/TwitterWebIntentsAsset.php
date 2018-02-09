<?php
/**
 * @copyright Copyright (C) 2015-2018 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\assets;

use yii\web\AssetBundle;

class TwitterWebIntentsAsset extends AssetBundle
{
    public $sourcePath = '@app/resources/.compiled/twitter';
    public $js = [
      'web-intents.js',
    ];
}
