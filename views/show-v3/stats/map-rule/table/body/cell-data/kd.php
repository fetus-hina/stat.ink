<?php

/**
 * @copyright Copyright (C) 2023-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\helpers\TypeHelper;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var View $this
 * @var array $stats
 */

$fmt = Yii::$app->formatter;

$f = fn (string $label, ?float $value, ?float $stddev): string => $value === null
  ? '-'
  : Html::tag(
    'span',
    $fmt->asDecimal($value, 1),
    [
      'class' => 'auto-tooltip',
      'title' => vsprintf('%s: %s', [
        $label,
        $stddev === null
          ? $fmt->asDecimal($value, 3)
          : vsprintf('%s (σ=%s)', [
            $fmt->asDecimal($value, 3),
            $fmt->asDecimal($stddev, 3),
          ]),
      ]),
    ],
  );

echo Html::tag(
  'div',
  implode(' / ', [
    $f(
      Yii::t('app', 'Avg Kills'),
      TypeHelper::floatOrNull(ArrayHelper::getValue($stats, 'kills', null)),
      TypeHelper::floatOrNull(ArrayHelper::getValue($stats, 'kill_stddev', null)),
    ),
    $f(
      Yii::t('app', 'Avg Deaths'),
      TypeHelper::floatOrNull(ArrayHelper::getValue($stats, 'deaths', null)),
      TypeHelper::floatOrNull(ArrayHelper::getValue($stats, 'death_stddev', null)),
    ),
  ]),
  [
    'class' => [
      'mb-1',
      'nobr',
      'small',
      'text-center',
    ],
  ],
);
