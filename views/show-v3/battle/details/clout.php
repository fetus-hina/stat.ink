<?php

declare(strict_types=1);

use app\components\i18n\Formatter;
use app\components\widgets\Label;
use app\components\widgets\FA;
use app\models\Battle3;
use yii\helpers\Html;

return [
  'label' => Yii::t('app', 'Clout'),
  'format' => 'raw',
  'value' => function (Battle3 $model): ?string {
    $festDragon = $model->festDragon;
    $cloutBefore = $model->clout_before;
    $cloutAfter = $model->clout_after;
    $cloutChange = $model->clout_change;

    if (
      $festDragon === null &&
      $cloutBefore === null &&
      $cloutAfter === null &&
      $cloutChange === null
    ) {
      return null;
    }

    $f = Yii::createObject([
      'class' => Formatter::class,
      'nullDisplay' => (string)FA::fas('question')->fw(),
    ]);

    $parts = [];
    if ($festDragon) {
      $parts[] = Label::widget([
        'content' => Yii::t('app', $festDragon->name),
        'color' => $festDragon->key === '333x' ? 'danger' : 'default',
      ]);
    }

    if ($cloutBefore !== null || $cloutAfter !== null || $cloutChange !== null) {
      // 貢献度の表示を何か行えるデータがある

      if ($cloutBefore === null && $cloutAfter === null) {
        // 増加分のみわかっている場合
        $parts[] = Html::encode(
          vsprintf('+%s', [
            $f->asInteger($cloutChange),
          ])
        );
      } else {
        $parts[] = trim(
          vsprintf('%s%s%s %s', [
            $f->asInteger($cloutBefore),
            (string)FA::fas('arrow-right')->fw(),
            $f->asInteger($cloutAfter),
            $cloutChange === null ? '' : sprintf('(+%s)', $f->asInteger($cloutChange)),
          ]),
        );
      }
    }

    return implode(' ', $parts);
  },
];
