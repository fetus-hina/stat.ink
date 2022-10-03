<?php

declare(strict_types=1);

use app\models\Battle3;
use yii\bootstrap\Html;

return [
  'label' => Yii::t('app', 'Clout'),
  'value' => function (Battle3 $model): ?string {
    $cloutBefore = $model->clout_before;
    $cloutAfter = $model->clout_after;
    $cloutChange = $model->clout_change;

    if ($cloutBefore === null && $cloutAfter === null && $cloutChange === null) {
      return null;
    }

    $f = Yii::$app->formatter;

    if ($cloutBefore === null && $cloutAfter === null) {
      // 増加分のみわかっている場合
      return vsprintf('+%s', [
        $f->asInteger($cloutChange),
      ]);
    }

    return implode(' ', [
      $cloutBefore !== null ? $f->asInteger($cloutBefore) : '?',
      '→',
      $cloutAfter !== null ? $f->asInteger($cloutAfter) : '?',
      $cloutChange !== null
        ? vsprintf('(+%s)', $f->asInteger($cloutChange))
        : '',
    ]);
  },
];
