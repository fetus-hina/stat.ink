<?php

/**
 * @copyright Copyright (C) 2022-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\models\Battle3;
use yii\helpers\Html;

return [
  'contentOptions' => ['class' => 'cell-main-weapon'],
  'format' => 'raw',
  'headerOptions' => ['class' => 'cell-main-weapon'],
  'label' => Yii::t('app', 'Weapon'),
  'value' => fn (Battle3 $model): string => $model->weapon
    ? Html::tag(
      'span',
      Html::encode(Yii::t('app-weapon3', $model->weapon->name ?? '?')),
      [
        'class' => 'auto-tooltip',
        'title' => vsprintf('%1$s %3$s / %2$s %4$s', [
          Yii::t('app', 'Sub:'),
          Yii::t('app', 'Special:'),
          $model->weapon->subweapon ? Yii::t('app-subweapon3', $model->weapon->subweapon->name) : '?',
          $model->weapon->special ? Yii::t('app-special3', $model->weapon->special->name) : '?',
        ]),
      ],
    )
    : Html::encode('?'),
];
