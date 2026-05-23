<?php

/**
 * @copyright Copyright (C) 2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
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

$configured = Yii::$app->params['discord']['read_enabled'] ?? false;
if (!$configured) {
  echo Html::tag(
    'tr',
    implode('', [
      Html::tag(
        'th',
        implode(' ', [
          Icon::discord(),
          Yii::t('app', 'Discord'),
        ]),
      ),
      Html::tag(
        'td',
        Html::encode(Yii::t('app', 'Not configured.')),
      ),
    ]),
  );
  return;
}

?>
<tr>
  <?= Html::tag(
    'th',
    implode(' ', [
      Icon::discord(),
      Html::encode(Yii::t('app', 'Discord')),
    ]),
  ) . "\n" ?>
  <?= Html::tag(
    'td',
    $user->loginWithDiscord
      ? $this->render('discord/integrated', compact('user'))
      : $this->render('discord/disabled', compact('user')),
  ) . "\n" ?>
</tr>
