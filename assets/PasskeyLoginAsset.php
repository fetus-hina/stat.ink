<?php

/**
 * @copyright Copyright (C) 2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\assets;

use yii\web\AssetBundle;
use yii\web\JqueryAsset;
use yii\web\YiiAsset;

class PasskeyLoginAsset extends AssetBundle
{
    public $sourcePath = '@app/resources/.compiled/stat.ink';
    public $css = [
        'passkey-login.css',
    ];
    public $js = [
        'passkey-login.js',
    ];
    public $depends = [
        FontAwesomeAsset::class,
        JqueryAsset::class,
        YiiAsset::class,
    ];
}
