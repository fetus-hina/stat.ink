<?php

/**
 * @copyright Copyright (C) 2022-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\models\Salmon3;

return fn (Salmon3 $model): array => [
  'class' => array_filter([
    'battle-row',
    $model->has_disconnect ? 'disconnected' : null,
  ]),
];
