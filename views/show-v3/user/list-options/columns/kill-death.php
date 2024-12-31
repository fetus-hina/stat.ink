<?php

/**
 * @copyright Copyright (C) 2022-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\models\Battle3;
use yii\helpers\Html;

return [
  'contentOptions' => ['class' => 'cell-kd nobr'],
  'format' => 'raw',
  'headerOptions' => ['class' => 'cell-kd'],
  'label' => Yii::t('app', 'k') . '/' . Yii::t('app', 'd'),
  'value' => fn (Battle3 $model): string => implode(' ', [
    Html::tag(
      'span', 
      $model->kill === null
        ? Html::encode('?')
        : ($model->death !== null && $model->kill >= $model->death
          ? Html::tag('strong', Html::encode($model->kill))
          : Html::encode($model->kill)
        ),
      ['class' => 'kill']
    ),
    '/',
    Html::tag(
      'span', 
      $model->death === null
        ? Html::encode('?')
        : ($model->kill !== null && $model->kill <= $model->death
          ? Html::tag('strong', Html::encode($model->death))
          : Html::encode($model->death)
        ),
      ['class' => 'death']
    ),
    $model->kill !== null && $model->death !== null
      ? (
        (function (int $k, int $d) {
          if ($k > $d) {
            return Html::tag('span', Html::encode('>'), ['class' => 'label label-success']);
          } elseif ($k < $d) {
            return Html::tag('span', Html::encode('<'), ['class' => 'label label-danger']);
          } else {
            return Html::tag('span', Html::encode('='), ['class' => 'label label-default']);
          }
        })($model->kill, $model->death)
      )
      : '',
  ]),
];
