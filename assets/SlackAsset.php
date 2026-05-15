<?php

/**
 * @copyright Copyright (C) 2016-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

namespace app\assets;

use yii\web\AssetBundle;
use yii\web\JqueryAsset;
use yii\web\YiiAsset;

final class SlackAsset extends AssetBundle
{
    /**
     * @var string
     */
    public $sourcePath = '@app/resources/.compiled/slack';

    /**
     * @var list<string>
     */
    public $js = [
        'slack.js',
    ];

    /**
     * @var list<class-string<AssetBundle>>
     */
    public $depends = [
        ApiFetchAsset::class,
        BootstrapToggleAsset::class,
        JqueryAsset::class,
        YiiAsset::class,
    ];
}
