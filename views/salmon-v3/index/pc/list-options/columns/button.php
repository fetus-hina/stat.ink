<?php

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
