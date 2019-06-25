<?php
declare(strict_types=1);

use yii\log\FileTarget;
use yii\web\HttpException;

return [
    'traceLevel' => YII_DEBUG ? 3 : 0,
    'targets' => [
        [
            'class' => FileTarget::class,
            'levels' => ['error', 'warning'],
            'except' => [
                sprintf('%s:404', HttpException::class),
            ],
        ],
    ],
];
