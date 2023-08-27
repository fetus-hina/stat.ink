<?php

declare(strict_types=1);

use app\models\Salmon3;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var View $this
 * @var int|null $value
 * @var string $label
 */

echo Html::tag(
  'span',
  vsprintf('%s %s', [
    $label,
    $value === null
      ? Html::encode('-')
      : Html::encode(Yii::$app->formatter->asInteger($value)),
  ]),
  ['class' => 'mr-2'],
);
