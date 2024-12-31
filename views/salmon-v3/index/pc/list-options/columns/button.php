<?php

/**
 * @copyright Copyright (C) 2022-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\grid\SalmonActionColumn;
use app\models\User;

/**
 * @var User $user
 */

return [
  'class' => SalmonActionColumn::class,
  'user' => $user,
  'contentOptions' => ['class' => 'nobr'],
];
