<?php

/**
 * @copyright Copyright (C) 2020-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\assets;

use yii\web\AssetBundle;
use yii\web\JqueryAsset;
use yii\web\YiiAsset;

class BattlePrivateNoteAsset extends AssetBundle
{
    public $sourcePath = '@app/resources/.compiled/stat.ink';
    public $js = [
        'battle-private-note.js',
    ];
    public $depends = [
        AppAsset::class,
        JqueryAsset::class,
        YiiAsset::class,
    ];
}
