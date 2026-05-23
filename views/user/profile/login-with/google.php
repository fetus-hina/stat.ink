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

$configured = Yii::$app->params['google']['read_enabled'] ?? false;
if (!$configured) {
  echo Html::tag(
    'tr',
    implode('', [
      Html::tag(
        'th',
        implode(' ', [
          Icon::google(),
          Yii::t('app', 'Google'),
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
      Icon::google(),
      Html::encode(Yii::t('app', 'Google')),
    ]),
  ) . "\n" ?>
  <?= Html::tag(
    'td',
    $user->loginWithGoogle
      ? $this->render('google/integrated', compact('user'))
      : $this->render('google/disabled', compact('user')),
  ) . "\n" ?>
</tr>
