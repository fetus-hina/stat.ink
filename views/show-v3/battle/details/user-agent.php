<?php

declare(strict_types=1);

use app\models\Battle3;
use yii\bootstrap\Html;

return [
  'label' => Yii::t('app', 'User Agent'),
  'format' => 'raw',
  'value' => function (Battle3 $model): ?string {
    if (!$model->agent) {
      return null;
    }

    return implode(' / ', [
      $model->agent->productUrl
        ? Html::a(
          Html::encode($model->agent->name),
          $model->agent->productUrl,
          [
            'target' => '_blank',
            'rel' => 'nofollow noopener',
          ]
        )
        : Html::encode($model->agent->name),
      $model->agent->versionUrl
        ? Html::a(
          Html::encode($model->agent->version),
          $model->agent->versionUrl,
          [
            'target' => '_blank',
            'rel' => 'nofollow',
          ]
        )
        : Html::encode($model->agent->version),
    ]);
  },
];
