<?php

declare(strict_types=1);

use app\models\Season3;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var Season3 $season
 * @var Season3[] $seasons
 * @var View $this
 * @var callable(Season3): string $seasonUrl
 */

?>
<?= Html::tag(
  'select',
  implode(
    '',
    ArrayHelper::getColumn(
      $seasons,
      fn (Season3 $model): string => Html::tag(
        'option',
        Html::encode(Yii::t('app-season3', $model->name)),
        [
          'selected' => $model->key === $season->key,
          'value' => $seasonUrl($model),
        ],
      ),
    ),
  ),
  [
    'class' => 'form-control mb-3',
    'onchange' => 'window.location.href = this.value',
  ],
);
