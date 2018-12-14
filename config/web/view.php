<?php
declare(strict_types=1);

use yii\web\View;

return [
    'class' => View::class,
    'renderers' => [
        'tpl' => require(__DIR__ . '/view-renderer/smarty.php'),
    ],
];
