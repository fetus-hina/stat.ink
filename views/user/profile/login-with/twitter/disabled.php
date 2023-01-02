<?php

declare(strict_types=1);

use app\components\widgets\Icon;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var View $this
 */

echo implode(' ', [
  Html::encode(Yii::t('app', 'Disabled')),
  Html::a(
    implode(' ', [
      Icon::appLink(),
      Html::encode(Yii::t('app', 'Integrate')),
    ]),
    ['update-login-with-twitter'],
    ['class' => 'btn btn-primary'],
  ),
]);
