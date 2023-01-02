<?php

declare(strict_types=1);

use app\components\web\AssetManager;
use jp3cki\yii2\datetimepicker\BootstrapDateTimePickerAsset;
use jp3cki\yii2\flot\BasePluginAsset;
use jp3cki\yii2\flot\ExcanvasAsset;
use jp3cki\yii2\flot\FlotAsset;
use jp3cki\yii2\flot\FlotCanvasAsset;
use jp3cki\yii2\flot\FlotCategoriesAsset;
use jp3cki\yii2\flot\FlotCrosshairAsset;
use jp3cki\yii2\flot\FlotErrorbarsAsset;
use jp3cki\yii2\flot\FlotFillbetweenAsset;
use jp3cki\yii2\flot\FlotImageAsset;
use jp3cki\yii2\flot\FlotNavigateAsset;
use jp3cki\yii2\flot\FlotPieAsset;
use jp3cki\yii2\flot\FlotResizeAsset;
use jp3cki\yii2\flot\FlotSelectionAsset;
use jp3cki\yii2\flot\FlotStackAsset;
use jp3cki\yii2\flot\FlotSymbolAsset;
use jp3cki\yii2\flot\FlotThresholdAsset;
use jp3cki\yii2\flot\FlotTimeAsset;
use jp3cki\yii2\jqueryColor\JqueryColorAsset;
use jp3cki\yii2\momentjs\MomentJsAsset;
use jp3cki\yii2\momentjs\MomentJsLocaleAsset;
use jp3cki\yii2\momentjs\MomentJsTimeZoneAsset;
use jp3cki\yii2\zxcvbn\ZxcvbnAsset;
use statink\yii2\calHeatmap\CalHeatmapAsset;
use statink\yii2\calHeatmap\D3Asset;
use statink\yii2\ipBadge\assets\Audiowide;
use statink\yii2\jdenticon\JdenticonAsset;
use statink\yii2\momentjs\MomentAsset;
use statink\yii2\momentjs\MomentTimezoneAsset;
use statink\yii2\sortableTable\JqueryStupidTableAsset;
use yii\bootstrap\BootstrapAsset;
use yii\bootstrap\BootstrapPluginAsset;
use yii\bootstrap\BootstrapThemeAsset;
use yii\validators\PunycodeAsset;
use yii\web\JqueryAsset;
use yii\widgets\MaskedInputAsset;
use yii\widgets\PjaxAsset;

return [
    'class' => AssetManager::class,
    'appendTimestamp' => true,
    'bundles' => [
        BootstrapDateTimePickerAsset::class => [
            'sourcePath' => '@node/eonasdan-bootstrap-datetimepicker/build',
        ],
        BasePluginAsset::class => [
            'sourcePath' => '@node/flot',
        ],
        ExcanvasAsset::class => [
            'sourcePath' => '@node/flot',
        ],
        FlotAsset::class => [
            'sourcePath' => '@node/flot',
        ],
        FlotCanvasAsset::class => [
            'sourcePath' => '@node/flot',
        ],
        FlotCategoriesAsset::class => [
            'sourcePath' => '@node/flot',
        ],
        FlotCrosshairAsset::class => [
            'sourcePath' => '@node/flot',
        ],
        FlotErrorbarsAsset::class => [
            'sourcePath' => '@node/flot',
        ],
        FlotFillbetweenAsset::class => [
            'sourcePath' => '@node/flot',
        ],
        FlotImageAsset::class => [
            'sourcePath' => '@node/flot',
        ],
        FlotNavigateAsset::class => [
            'sourcePath' => '@node/flot',
        ],
        FlotPieAsset::class => [
            'sourcePath' => '@node/flot',
        ],
        FlotResizeAsset::class => [
            'sourcePath' => '@node/flot',
        ],
        FlotSelectionAsset::class => [
            'sourcePath' => '@node/flot',
        ],
        FlotStackAsset::class => [
            'sourcePath' => '@node/flot',
        ],
        FlotSymbolAsset::class => [
            'sourcePath' => '@node/flot',
        ],
        FlotThresholdAsset::class => [
            'sourcePath' => '@node/flot',
        ],
        FlotTimeAsset::class => [
            'sourcePath' => '@node/flot',
        ],
        JqueryColorAsset::class => [
            'sourcePath' => '@node/jquery-color/dist',
        ],
        MomentJsAsset::class => [
            'sourcePath' => '@node/moment/min',
        ],
        MomentJsLocaleAsset::class => [
            'sourcePath' => '@node/moment/locale',
        ],
        MomentJsTimeZoneAsset::class => [
            'sourcePath' => '@node/moment-timezone/builds',
        ],
        ZxcvbnAsset::class => [
            'sourcePath' => '@node/zxcvbn/dist',
        ],
        CalHeatmapAsset::class => [
            'sourcePath' => '@node/cal-heatmap',
        ],
        D3Asset::class => [
            'sourcePath' => '@node/d3',
        ],
        Audiowide::class => [
            'css' => [
                'https://fonts.stats.ink/audiowide/audiowide.min.css',
            ],
        ],
        JdenticonAsset::class => [
            'sourcePath' => '@node/jdenticon/dist',
        ],
        JqueryStupidTableAsset::class => [
            'sourcePath' => '@node/stupid-table-plugin',
        ],
        BootstrapAsset::class => [
            'sourcePath' => '@node/bootstrap/dist',
            'css' => [
                'css/bootstrap.min.css',
            ],
        ],
        BootstrapPluginAsset::class => [
            'sourcePath' => '@node/bootstrap/dist',
            'js' => [
                'js/bootstrap.min.js',
            ],
        ],
        BootstrapThemeAsset::class => [
            'sourcePath' => '@node/bootstrap/dist',
        ],
        PunycodeAsset::class => [
            'sourcePath' => '@node/punycode',
        ],
        JqueryAsset::class => [
            'sourcePath' => '@node/jquery/dist',
            'js' => [
                'jquery.min.js',
            ],
        ],
        MaskedInputAsset::class => [
            'sourcePath' => '@node/inputmask/dist',
        ],
        PjaxAsset::class => [
            'sourcePath' => '@node/yii2-pjax',
        ],
        MomentAsset::class => [
            'sourcePath' => '@node/moment/min',
        ],
        MomentTimezoneAsset::class => [
            'sourcePath' => '@node/moment-timezone/builds',
        ],
    ],
];
