<?php

declare(strict_types=1);

use app\components\widgets\Icon;
use app\models\Battle3;
use yii\base\Model;
use yii\helpers\Html;

$render = function (?Model $model, string $catalog): string {
  if (!$model) {
    return Html::encode('?');
  }

  return Html::encode(Yii::t($catalog, $model->name));
};

return [
  'label' => Yii::t('app-lobby3', 'Challenge'),
  'value' => function (Battle3 $model) use ($render): ?string {
    if (!$event = $model->event) {
      return null;
    }

    // TODO: translate
    return $event->name;
  },
];
