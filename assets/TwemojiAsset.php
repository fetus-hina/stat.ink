<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\assets;

use yii\web\AssetBundle;

final class TwemojiAsset extends AssetBundle
{
    public $sourcePath = '@node/twemoji/dist';
    public $js = [
        'twemoji.min.js',
    ];
}
