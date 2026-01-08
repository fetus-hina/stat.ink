<?php

/**
 * @copyright Copyright (C) 2022-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\widgets\Icon;
use app\models\User;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var User $user
 * @var View $this
 */

?>
<?= Html::a(
  implode(' ', [
    Icon::list(),
    Html::encode(Yii::t('app', 'Simplified List')),
  ]),
  ['show-v3/user',
    'screen_name' => $user->screen_name,
    'v' => 'simple',
  ],
  [
    'class' => [
      'btn',
      'btn-default',
    ],
    'rel' => 'nofollow',
  ],
) ?>
