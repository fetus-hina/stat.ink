<?php

/**
 * @copyright Copyright (C) 2018-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\web\CsvResponseFormatter;
use app\components\web\IkalogJsonResponseFormatter;
use app\components\web\PrettyJsonResponseFormatter;
use app\components\web\Response;
use app\components\web\YamlResponseFormatter;
use yii\web\JsonResponseFormatter;

return [
    'class' => Response::class,
    'formatters' => [
        'compact-json' => JsonResponseFormatter::class,
        'csv' => CsvResponseFormatter::class,
        'ikalog-json' => IkalogJsonResponseFormatter::class,
        'json' => PrettyJsonResponseFormatter::class,
        'yaml' => YamlResponseFormatter::class,
    ],
];
