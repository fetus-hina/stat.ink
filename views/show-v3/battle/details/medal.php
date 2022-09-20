<?php

declare(strict_types=1);

use app\models\Battle3;
use app\models\BattleMedal3;
use yii\bootstrap\Html;

return [
  'label' => Yii::t('app', 'Medals'),
  'format' => 'raw',
  'value' => function (Battle3 $model): ?string {
    $intermediates = BattleMedal3::find()
      ->with('medal')
      ->andWhere(['battle_id' => $model->id])
      ->orderBy(['id' => SORT_ASC])
      ->all();

    if (!$intermediates) {
      return null;
    }

    return implode('<br>', array_map(
      fn (BattleMedal3 $intermediate): string => Html::encode($intermediate->medal->name),
      $intermediates
    ));
  },
];
