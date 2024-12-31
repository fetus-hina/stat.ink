<?php

/**
 * @copyright Copyright (C) 2021-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\assets\UserStat2MonthlyReportAsset;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var View $this
 * @var int $battles
 * @var int $wins
 */

UserStat2MonthlyReportAsset::register($this);

echo implode("\n", [
  Html::tag('div', '', [
    'class' => 'pie-chart win-pct',
    'data' => [
      'values' => [
        'win' => $wins,
        'lose' => $battles - $wins,
      ],
      'labels' => [
        'win' => Yii::t('app', 'Win'),
        'lose' => Yii::t('app', 'Lose'),
      ],
    ],
  ]),
  Html::tag(
    'p',
    Html::encode(vsprintf('n=%s', [
      Yii::$app->formatter->asInteger($battles),
    ])),
    ['class' => 'text-center small text-muted font-italic']
  ),
  '',
]);
