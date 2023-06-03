<?php

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
