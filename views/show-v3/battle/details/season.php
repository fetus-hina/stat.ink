<?php

declare(strict_types=1);

use app\components\widgets\Icon;
use app\models\Battle3;
use app\models\Battle3FilterForm;
use app\models\Season3;
use yii\db\Expression as DbExpr;
use yii\helpers\Html;

return [
  'label' => Yii::t('app', 'Season'),
  'format' => 'raw',
  'value' => function (Battle3 $model): ?string {
    if ($model->start_at === null) {
      return null;
    }

    $at = new DbExpr(
      vsprintf('%s::TIMESTAMPTZ', [
        Yii::$app->db->quoteValue((string)$model->start_at),
      ]),
    );

    $season = Season3::find()
      ->andWhere(['@>', '{{%season3}}.[[term]]', $at])
      ->limit(1)
      ->one();
    if (!$season) {
        return null;
    }

    return implode(' ', [
      Html::a(
        Icon::search(),
        ['/show-v3/user',
          'screen_name' => $model->user->screen_name,
          'f' => [
            'lobby' => $model->lobby?->key ?? null,
            'rule' => $model->rule?->key ?? null,
            'term' => vsprintf('%s%s', [
              Battle3FilterForm::PREFIX_TERM_SEASON,
              $season->key,
            ]),
          ],
        ],
      ),
      Html::encode(Yii::t('app-season3', $season->name)),
    ]);
  },
];
