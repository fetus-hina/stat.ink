<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\assets;

use yii\web\AssetBundle;

final class LineSeedEnAsset extends AssetBundle
{
    public $sourcePath = '@app/resources/line-seed-en';
    public $css = [
        'lineseedsans-bd.min.css',
        'lineseedsans-rg.min.css',
    ];
}
