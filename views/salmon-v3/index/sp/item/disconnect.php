<?php

declare(strict_types=1);

use app\components\widgets\Icon;
use app\models\Salmon3;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var Salmon3 $model
 * @var View $this
 */

echo Html::tag(
  'div',
  $model->has_disconnect ? Icon::hasDisconnected() : '',
  [
    'class' => [
      'simple-battle-disconnected',
      'text-danger',
    ],
  ],
);
