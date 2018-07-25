<?php
/**
 * @copyright Copyright (C) 2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\assets;

use yii\web\AssetBundle;

class FontAwesomeAsset extends AssetBundle
{
    public $sourcePath = '@npm/fortawesome--fontawesome-free';
    public $css = [
        'css/all.min.css',
    ];
    public $publishOptions = [
        'only' => [
            'css/*',
            'webfonts/*',
        ],
    ];
}
