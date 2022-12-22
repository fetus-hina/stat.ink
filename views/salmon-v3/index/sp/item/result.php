<?php

declare(strict_types=1);

use app\components\widgets\Label;
use app\models\Salmon3;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;

/**
 * @var Salmon3 $model
 * @var View $this
 */

// Unknown result
if ($model->clear_waves === null) {
  echo Html::tag(
    'div',
    Html::encode('?'),
    [
      'class' => [
        'simple-battle-result',
        'simple-battle-result-unk',
      ],
    ],
  );

  return;
}

// Failed
if ($model->clear_waves < 3) {
  echo Html::tag(
    'div',
    implode('', [
      Yii::t(
        'app-salmon2',
        'Failed<br><small>in wave {waveNumber}</small>',
        ['waveNumber' => $model->clear_waves + 1],
      ),
      $model->fail_reason_id
        ? Html::tag(
          'div',
          Label::widget([
            'content' => Yii::t('app-salmon2', $model->failReason->short_name),
            'color' => $model->failReason->color,
            'options' => [
              'style' => [
                'font-size' => '11px',
                'font-weight' => 'normal',
              ],
            ],
          ]),
        )
        : '',
    ]),
    [
      'class' => [
        'simple-battle-result',
        'simple-battle-result-lost',
      ],
    ],
  );

  return;
}

// Cleared, King Salmonid appearances
if ($model->kingSalmonid) {
  echo Html::tag(
    'div',
    implode('<br>', [
      Html::encode(Yii::t('app-salmon2', 'Cleared')),
      Html::tag(
        'small',
        Html::encode(Yii::t('app-salmon-boss3', $model->kingSalmonid->name)),
        [
          'class' => $model->clear_extra === null
            ? 'simple-battle-result-unk'
            : ($model->clear_extra ? 'simple-battle-result-won' : 'simple-battle-result-lost'),
        ],
      ),
    ]),
    [
      'class' => [
        'simple-battle-result',
        'simple-battle-result-won',
      ],
    ],
  );

  return;
}

// Cleared
echo Html::tag(
  'div',
  Html::encode(Yii::t('app-salmon2', 'Cleared')),
  [
    'class' => [
      'simple-battle-result',
      'simple-battle-result-won',
    ],
  ],
);
