<?php

declare(strict_types=1);

use app\models\AgentVariable3;
use app\models\Battle3;
use yii\bootstrap\Html;

return [
  'label' => Yii::t('app', 'Extra Data'),
  'format' => 'raw',
  'value' => function (Battle3 $model): ?string {
    $list = $model->variables;
    if (!$list) {
      return null;
    }

    usort($list, fn (AgentVariable3 $a, AgentVariable3 $b): int => strnatcmp($a->key, $b->key));

    return Html::tag(
      'table',
      Html::tag(
        'tbody',
        implode('', array_map(
          fn (AgentVariable3 $model): string => Html::tag(
            'tr',
            implode('', [
              Html::tag('th', Html::encode(Yii::t('app-ua-vars', $model->key))),
              Html::tag('td', Html::encode(Yii::t('app-ua-vars-v', $model->value))),
            ])
          ),
          $list
        ))
      ),
      [
        'class' => 'table',
        'style' => 'margin-bottom:0',
      ]
    );
  },
];
