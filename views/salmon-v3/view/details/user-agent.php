<?php

/**
 * @copyright Copyright (C) 2022-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\models\Salmon3;
use yii\bootstrap\Html;

return [
  'label' => Yii::t('app', 'User Agent'),
  'format' => 'raw',
  'value' => function (Salmon3 $model): ?string {
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
