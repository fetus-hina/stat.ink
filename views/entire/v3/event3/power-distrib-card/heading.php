<?php

declare(strict_types=1);

use yii\helpers\Html;
use yii\web\View;

/**
 * @var View $this
 */

echo Html::tag(
  'div',
  Html::encode(Yii::t('app', 'Challenge Power Distribution')),
  ['class' => 'panel-heading'],
);
