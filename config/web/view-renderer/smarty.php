<?php
declare(strict_types=1);

use app\components\widgets\AdWidget;
use app\components\widgets\BattleFilterWidget;
use app\components\widgets\SnsWidget;
use app\components\widgets\WinLoseLegend;
use yii\smarty\ViewRenderer;

return [
    'class' => ViewRenderer::class,
    'options' => [
        'force_compile' => false,
        'left_delimiter' => '{{',
        'right_delimiter' => '}}',
    ],
    'pluginDirs' => [
        '//smarty/',
    ],
    'widgets' => [
        'functions' => [
            'AdWidget' => AdWidget::class,
            'BattleFilterWidget' => BattleFilterWidget::class,
            'SnsWidget' => SnsWidget::class,
            'WinLoseLegend' => WinLoseLegend::class,
        ]
    ],
];
