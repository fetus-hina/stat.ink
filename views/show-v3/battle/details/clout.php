<?php

/**
 * @copyright Copyright (C) 2022-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\i18n\Formatter;
use app\components\widgets\Label;
use app\components\widgets\Icon;
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
    $conchClash = $model->conchClash;

    if (
      $festDragon === null &&
      $cloutBefore === null &&
      $cloutAfter === null &&
      $cloutChange === null &&
      $conchClash === null
    ) {
      return null;
    }

    $f = Yii::createObject([
      'class' => Formatter::class,
      'nullDisplay' => Icon::unknown(),
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
          implode(' ', [
            $f->asInteger($cloutBefore),
            Icon::arrowRight(),
            $f->asInteger($cloutAfter),
            $cloutChange === null ? '' : sprintf('(+%s)', $f->asInteger($cloutChange)),
          ]),
        );
      }
    }

    if ($conchClash) {
      $parts[] = Label::widget([
        'content' => Yii::t('app-conch-clash3', $conchClash->name),
        'color' => match ($conchClash->key) {
          '33x' => 'danger',
          '10x' => 'warning',
          default => 'default',
        },
      ]);
    }

    return implode(' ', $parts);
  },
];
