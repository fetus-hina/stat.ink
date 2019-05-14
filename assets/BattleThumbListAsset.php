<?php
/**
 * @copyright Copyright (C) 2015-2019 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

declare(strict_types=1);

namespace app\assets;

use yii\web\AssetBundle;

class BattleThumbListAsset extends AssetBundle
{
    public $sourcePath = '@app/resources/.compiled/stat.ink';
    public $js = [
        'battle-thumb-list.js',
    ];
    public $css = [
        'battle-thumb-list.css',
        'game-modes.css',
    ];
    public $depends = [
        AppAsset::class,
    ];
}
