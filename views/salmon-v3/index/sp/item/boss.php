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

$value = ArrayHelper::getValue($model, ['salmonPlayer3s', 0, 'defeat_boss']);

echo Html::tag(
  'span',
  vsprintf('%s %s', [
    Icon::s3BossSalmonid('bakudan', alt: Yii::t('app-salmon3', 'Bosses defeated')),
    is_int($value)
      ? Html::encode(Yii::$app->formatter->asInteger($value))
      : Html::encode('-'),
  ]),
  ['class' => 'mr-2'],
);
