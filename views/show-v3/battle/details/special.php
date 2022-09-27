<?php

declare(strict_types=1);

use app\components\widgets\v3\weaponIcon\SpecialIcon;
use app\models\Battle3;
use yii\helpers\Html;

return [
  'label' => Yii::t('app', 'Specials'),
  'format' => 'raw',
  'value' => function (Battle3 $model): ?string {
    if ($model->special === null) {
      return null;
    }

    $weapon = $model->weapon;
    if ($weapon) {
      $special = $weapon->special;
      if ($special) {
        return vsprintf('%s (%s %s)', [
          Html::encode(Yii::$app->formatter->asInteger($model->special)),
          SpecialIcon::widget(['model' => $special, 'alt' => false]),
          Html::encode(Yii::t('app-special3', $special->name)),
        ]);
      }
    }

    return Html::encode(Yii::$app->formatter->asInteger($model->special));
  },
];
