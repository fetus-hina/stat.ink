<?php

/**
 * @copyright Copyright (C) 2023-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use yii\i18n\DbMessageSource;

return [
    'class' => DbMessageSource::class,

    'cache' => 'messageCache',
    'cachingDuration' => 7200,
    'enableCaching' => true,
    'messageTable' => '{{%translate_message}}',
    'sourceMessageTable' => '{{%translate_source_message}}',
];
