<?php

declare(strict_types=1);

use app\components\widgets\v3\WeaponName;
use app\models\Battle3;
use yii\bootstrap\Html;

return [
  'attribute' => 'weapon_id',
  'label' => Yii::t('app', 'Weapon'),
  'format' => 'raw',
  'value' => function (Battle3 $model): string {
    $weapon = $model->weapon;
    return $weapon
      ? WeaponName::widget([
        'model' => $weapon,
        'showName' => true,
        'subInfo' => true,
      ])
      : Html::encode('?');
  },
];
