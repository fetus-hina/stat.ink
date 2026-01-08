<?php

/**
 * @copyright Copyright (C) 2015-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use yii\debug\Module as DebugModule;
use yii\gii\Module as GiiModule;

$params = require __DIR__ . '/params.php';
$config = [
    'name' => 'stat.ink',
    'version' => @file_exists(__DIR__ . '/version.php')
        ? require(__DIR__ . '/version.php')
        : 'UNKNOWN',
    'id' => 'statink',
    'language' => 'ja-JP',
    'timeZone' => 'Asia/Tokyo',
    'basePath' => dirname(__DIR__),
    'bootstrap' => [
        'log',
        'theme',
        require __DIR__ . '/web/bootstrap/alias.php',
    ],
    'aliases' => require(__DIR__ . '/web/alias.php'),
    'components' => [
        'assetManager' => require(__DIR__ . '/web/asset-manager.php'),
        'cache' => require(__DIR__ . '/web/cache.php'),
        'db' => require(__DIR__ . '/db.php'),
        'errorHandler' => require(__DIR__ . '/web/error-handler.php'),
        'formatter' => require(__DIR__ . '/web/formatter.php'),
        'geoip' => require(__DIR__ . '/web/geoip.php'),
        'i18n' => require(__DIR__ . '/i18n.php'),
        'log' => require(__DIR__ . '/web/log.php'),
        'mailer' => require(__DIR__ . '/web/mailer.php'),
        'messageCache' => require(__DIR__ . '/web/message-cache.php'),
        'mutex' => require(__DIR__ . '/web/mutex.php'),
        'pgMutex' => require(__DIR__ . '/web/mutex-pgsql.php'),
        'queue' => require(__DIR__ . '/queue.php'),
        'request' => require(__DIR__ . '/web/request.php'),
        'response' => require(__DIR__ . '/web/response.php'),
        'schemaCache' => require(__DIR__ . '/web/schema-cache.php'),
        'session' => require(__DIR__ . '/web/session.php'),
        'theme' => require(__DIR__ . '/web/theme.php'),
        'urlManager' => require(__DIR__ . '/web/url-manager.php'),
        'user' => require(__DIR__ . '/web/user.php'),
        'view' => require(__DIR__ . '/web/view.php'),
        'weaponShortener' => require(__DIR__ . '/web/weapon-shortener.php'),
    ],
    'params' => $params,
];

if (defined('YII_ENV_DEV') && YII_ENV_DEV) {
    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = GiiModule::class;

    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => DebugModule::class,
        'allowedIPs' => require(__DIR__ . '/debug-ips.php'),
    ];
}

return $config;
