<?php

declare(strict_types=1);

use app\components\helpers\Battle as BattleHelper;
use app\components\widgets\Icon;
use app\models\Battle3;
use app\models\Battle3FilterForm;
use app\models\Season3;
use yii\db\Expression as DbExpr;
use yii\helpers\Html;

return [
  'label' => Yii::t('app', 'Period'),
  'format' => 'raw',
  'value' => function (Battle3 $model): ?string {
    if (!$model->period) {
      return null;
    }

    [$from, $to] = BattleHelper::periodToRange2($model->period);
    return implode(' ', [
      Html::a(
        Icon::search(),
        ['/show-v3/user',
          'screen_name' => $model->user->screen_name,
          'f' => [
            'lobby' => $model->lobby?->key ?? null,
            'rule' => $model->rule?->key ?? null,
            'term' => 'term',
            'term_from' => '@' . (string)$from,
            'term_to' => '@' . (string)($to - 1),
          ],
        ],
      ),
      Html::encode(
        Yii::t('app', '{from} - {to}', [
          'from' => Yii::$app->formatter->asDateTime($from, 'short', 'short'),
          'to' => Yii::$app->formatter->asTime($to, 'short'),
        ]),
      ),
    ]);
  },
];
