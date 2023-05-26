<?php

declare(strict_types=1);

use app\assets\SalmonEggAsset;
use app\models\Salmon3;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var View $this
 * @var int|null $value
 * @var string $icon
 * @var string $label
 */

echo Html::tag(
  'span',
  vsprintf('%s %s', [
    Html::encode(Html::encode($label)),
    $value === null
      ? Html::encode('-')
      : Html::encode(Yii::$app->formatter->asInteger($value)),
  ]),
  ['class' => 'mr-2'],
);
