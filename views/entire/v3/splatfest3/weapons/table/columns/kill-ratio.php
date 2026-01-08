<?php

/**
 * @copyright Copyright (C) 2024-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\widgets\KillRatioBadgeWidget;
use app\models\Splatfest3StatsWeapon;
use yii\base\Model;
use yii\helpers\Html;

return [
  'contentOptions' => fn (Splatfest3StatsWeapon $model): array => [
    'class' => 'text-right',
    'data-sort-value' => $model->avg_death > 0 ? $model->avg_kill / $model->avg_death : '',
  ],
  'format' => 'raw',
  'headerOptions' => [
    'data-sort' => 'float',
    'data-sort-default' => 'desc',
  ],
  'label' => Yii::t('app', 'Kill Ratio'),
  'value' => function (Splatfest3StatsWeapon $model): string {
    $kr = $model->avg_death > 0 ? $model->avg_kill / $model->avg_death : null;
    if ($kr === null) {
      return '';
    }

    return implode(' ', [
      Html::encode(Yii::$app->formatter->asDecimal($kr, 3)),
      KillRatioBadgeWidget::widget(['killRatio' => $kr]),
    ]);
  },
];
