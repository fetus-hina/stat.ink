<?php
/**
 * @copyright Copyright (C) 2015-2018 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\assets;

use yii\web\AssetBundle;

class RpgAwesomeAsset extends AssetBundle
{
    public $sourcePath = '@node/rpg-awesome';
    public $css = [
        'css/rpg-awesome.css',
    ];
    public $publishOptions = [
        'only' => [
            'css/*',
            'fonts/*',
        ],
    ];
}
