<?php

declare(strict_types=1);

use app\components\widgets\FA;
use app\models\Battle3;
use yii\bootstrap\Html;

return [
  'label' => Yii::t('app', 'Stats'),
  'format' => 'raw',
  'value' => function (Battle3 $model): string {
    $lobby = $model->lobby;
    $weapon = $model->weapon;
    $map = $model->map;
    $result = $model->result;
    if (!$lobby || !$weapon || !$map || !$result) {
      return implode('', [
        Html::tag('span', (string)FA::fas('times')->fw(), ['class' => 'text-danger']),
        Html::encode(Yii::t('app', 'Incomplete Data')),
      ]);
    }

    if ($lobby->key === 'private') {
      return implode('', [
        Html::tag('span', (string)FA::fas('times')->fw(), ['class' => 'text-danger']),
        Html::encode(Yii::t('app-lobby3', 'Private Battle')),
      ]);
    }

    if (!$result->aggregatable) {
      return implode('', [
        Html::tag('span', (string)FA::fas('times')->fw(), ['class' => 'text-danger']),
        Html::encode(Yii::t('app', $result->name)),
      ]);
    }

    if ($model->has_disconnect) {
      return implode('', [
        Html::tag('span', (string)FA::fas('times')->fw(), ['class' => 'text-danger']),
        Html::encode(Yii::t('app', 'Disconnected')),
      ]);
    }

    $f = function (string $label, bool $value): string {
      return vsprintf('%s: %s%s', [
        Html::encode($label),
        $value
          ? Html::tag('span', (string)FA::fas('check')->fw(), ['class' => 'text-success'])
          : Html::tag('span', (string)FA::fas('times')->fw(), ['class' => 'text-danger']),
        Html::encode(Yii::t('yii', $value ? 'Yes' : 'No')),
      ]);
    };
    return implode('<br>', [
      $f(Yii::t('app', 'Automated'), $model->is_automated),
      $f(Yii::t('app', 'Used in global stats'), $model->is_automated && $model->use_for_entire),
    ]);
  },
];
