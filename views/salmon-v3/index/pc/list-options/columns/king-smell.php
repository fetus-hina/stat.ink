<?php

/**
 * @copyright Copyright (C) 2022-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\widgets\Icon;
use app\models\Salmon3;
use yii\helpers\Html;

return [
  '-label' => Yii::t('app-salmon3', 'Salmometer'),
  'contentOptions' => ['class' => 'cell-king-smell text-center'],
  'format' => 'raw',
  'headerOptions' => ['class' => 'cell-king-smell'],
  'label' => '',
  'value' => function (Salmon3 $model): ?string {
    $meter = $model->king_smell;
    return is_int($meter) && 0 <= $meter && $meter <= 5
      ? Icon::s3Salmometer(
        $meter,
        // オカシラゲージの個別表示は v6.0.0 から
        version_compare($model->version?->tag ?? '0.0.0', '6.0.0', '>=')
          ? $model->schedule?->king
          : null,
      )
      : null;
  },
];
