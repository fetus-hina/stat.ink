<?php

declare(strict_types=1);

use yii\log\FileTarget;
use yii\web\HttpException;

return [
    'traceLevel' => defined('YII_DEBUG') && YII_DEBUG ? 3 : 0,
    'targets' => [
        [
            'class' => FileTarget::class,
            'levels' => ['error', 'warning'],
            'except' => array_merge(
                array_map(
                    fn (int $httpStatus): string => sprintf('%s:%03d', HttpException::class, $httpStatus),
                    [400, 401, 403, 404, 405, 410, 414]
                ),
                [
                ],
            ),
        ],
    ],
];
