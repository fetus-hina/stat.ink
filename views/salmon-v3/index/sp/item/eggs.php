<?php

declare(strict_types=1);

use app\components\widgets\Icon;
use app\models\Salmon3;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var Salmon3 $model
 * @var View $this
 */

echo Html::tag(
  'div',
  implode('', [
    $this->render('eggs-egg', [
      'label' => Icon::goldenEgg(),
      'value' => ArrayHelper::getValue($model, 'salmonPlayer3s.0.golden_eggs'),
    ]),
    $this->render('eggs-egg', [
      'label' => Icon::powerEgg(),
      'value' => ArrayHelper::getValue($model, 'salmonPlayer3s.0.power_eggs'),
    ]),
    $this->render('boss', compact('model')),
  ]),
  [
    'class' => [
      'omit',
      'simple-battle-kill-death',
    ],
  ],
);
