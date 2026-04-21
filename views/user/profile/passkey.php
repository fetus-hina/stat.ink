<?php

/**
 * @copyright Copyright (C) 2026 AIZAWA Hina
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

$count = $user->getUserPasskeys()->count();
?>
<h2>
  <?= Html::encode(Yii::t('app-passkey', 'Passkeys')) . "\n" ?>
  <?= Html::a(
    implode(' ', [
      Icon::edit(),
      Html::encode(Yii::t('app-passkey', 'Manage')),
    ]),
    ['user/passkey'],
    ['class' => 'btn btn-primary btn-sm'],
  ) . "\n" ?>
</h2>
<p>
  <?= Html::encode(
    Yii::t(
      'app-passkey',
      '{n, plural, =0{No passkeys registered.} one{# passkey registered.} other{# passkeys registered.}}',
      ['n' => (int)$count],
    ),
  ) . "\n" ?>
</p>
