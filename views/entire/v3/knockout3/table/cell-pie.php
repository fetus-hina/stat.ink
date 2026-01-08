<?php

/**
 * @copyright Copyright (C) 2022-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\models\Knockout3;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\web\View;

/**
 * @var Knockout3|null $model
 * @var View $this
 */

if (!$model || $model->battles <= 0) {
  return '';
}

echo implode('', [
  Html::tag('div', '', [
    'class' => 'pie-flot-container',
    'data' => [
      'json' => Json::encode([
        'battle' => (int)$model->battles,
        'ko' => (int)$model->knockout,
      ]),
    ],
  ]),
  $model->avg_battle_time > 0
    ? Html::tag(
      'p',
      $model->stddev_battle_time > 0
        ? vsprintf('%s <small>(σ=%s)</small>', [
          Html::encode(
            Yii::t('app', 'Avg. game in {time}', [
              'time' => Yii::$app->formatter->asDecimal((float)$model->avg_battle_time, 1),
            ]),
          ),
          Yii::$app->formatter->asDecimal((float)$model->stddev_battle_time, 1),
        ])
        : Yii::t('app', 'Avg. game in {time}', [
          'time' => Yii::$app->formatter->asDecimal((float)$model->avg_battle_time, 1),
        ]),
      ['class' => 'm-0 mt-1 small text-center'],
    )
    : '',
  $model->avg_knockout_time > 0
    ? Html::tag(
      'p',
      $model->stddev_knockout_time > 0
        ? vsprintf('%s <small>(σ=%s)</small>', [
          Html::encode(
            Yii::t('app', 'Avg. K.O. in {time}', [
              'time' => Yii::$app->formatter->asDecimal((float)$model->avg_knockout_time, 1),
            ]),
          ),
          Yii::$app->formatter->asDecimal((float)$model->stddev_knockout_time, 1),
        ])
        : Yii::t('app', 'Avg. K.O. in {time}', [
          'time' => Yii::$app->formatter->asDecimal((float)$model->avg_knockout_time, 1),
        ]),
      ['class' => 'm-0 mt-1 small text-center'],
    )
    : '',
]);
