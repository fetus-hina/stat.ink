<?php

/**
 * @copyright Copyright (C) 2015-2020 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 * @codingStandardsIgnoreFile
 */

declare(strict_types=1);

use app\components\web\Application;

if (file_exists(__DIR__ . '/../.staging')) {
    defined('YII_DEBUG') or define('YII_DEBUG', false);
    defined('YII_ENV') or define('YII_ENV', 'test');
} elseif (!file_exists(__DIR__ . '/../.production')) {
    defined('YII_DEBUG') or define('YII_DEBUG', true);
    defined('YII_ENV') or define('YII_ENV', 'dev');
}
ini_set('display_errors', (string)1);
ini_set('serialize_precision', (string)-1);

if (@file_exists(__DIR__ . '/../.maintenance')) {
    header('HTTP/1.1 503 Service Unavailable');
    header('Content-Type: text/plain; charset=UTF-8');
    echo "This website is under maintenance.\n";
    echo "It will be back soon.\n";
    echo "\n";
    echo "メンテナンス中です。\n";
    exit(0);
}

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../vendor/yiisoft/yii2/Yii.php';
require __DIR__ . '/../config/web-bootstrap.php';

$config = require __DIR__ . '/../config/web.php';

require __DIR__ . '/../components/web/Application.php';
(new Application($config))->run();
