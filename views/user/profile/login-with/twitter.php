<?php

/**
 * @copyright Copyright (C) 2023-2026 AIZAWA Hina
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

$configured = Yii::$app->params['twitter']['read_enabled'] ?? false;
if (!$configured) {
  echo Html::tag(
    'tr',
    implode('', [
      Html::tag(
        'th',
        implode(' ', [
          Icon::twitter(),
          Yii::t('app', 'Twitter'),
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
      Icon::twitter(),
      Html::encode(Yii::t('app', 'Twitter')),
    ]),
  ) . "\n" ?>
  <?= Html::tag(
    'td',
    $user->loginWithTwitter
      ? $this->render('twitter/integrated', compact('user'))
      : $this->render('twitter/disabled', compact('user')),
  ) . "\n" ?>
</tr>
