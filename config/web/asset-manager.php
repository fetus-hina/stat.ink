<?php
declare(strict_types=1);

use app\components\web\AssetConverter;
use app\components\web\AssetManager;
use yii\bootstrap\BootstrapAsset;
use yii\bootstrap\BootstrapPluginAsset;
use yii\web\JqueryAsset;

return [
    'class' => AssetManager::class,
    'appendTimestamp' => true,
    'bundles' => [
        JqueryAsset::class => [
            'js' => [
                'jquery.min.js',
            ],
        ],
        BootstrapAsset::class => [
            'css' => [
                'css/bootstrap.min.css',
            ],
        ],
        BootstrapPluginAsset::class => [
            'js' => [
                'js/bootstrap.min.js',
            ],
        ],
    ],
    'converter' => [
        'class' => AssetConverter::class,
    ],
];
