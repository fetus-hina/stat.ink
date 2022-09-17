<?php

declare(strict_types=1);

use app\models\Battle3;

return [
  'label' => Yii::t('app', 'Mode'),
  'value' => function (Battle3 $model): string {
    $rule = $model->rule;
    $lobby = $model->lobby;

    return vsprintf('%s - %s', [
      $rule ? Yii::t('app-rule3', $rule->name) : '?',
      $lobby ? Yii::t('app-lobby3', $lobby->name) : '?',
    ]);
  },
];
