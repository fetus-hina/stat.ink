<?php

/**
 * @copyright Copyright (C) 2023-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\assets;

use yii\web\AssetBundle;
use yii\web\JqueryAsset;

final class JqueryEasyChartjsAsset extends AssetBundle
{
    /**
     * @var string
     */
    public $sourcePath = '@app/resources/.compiled/stat.ink';

    /**
     * @var string[]
     */
    public $js = [
        'jquery.easy-chartjs.js',
    ];

    /**
     * @var AssetBundle[]
     */
    public $depends = [
        ChartJsAsset::class,
        JqueryAsset::class,
    ];
}
