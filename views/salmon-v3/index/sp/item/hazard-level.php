<?php

/**
 * @copyright Copyright (C) 2022-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\models\Salmon3;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var Salmon3 $model
 * @var View $this
 */

echo Html::tag(
  'div',
  Html::encode(
    vsprintf('%s: %s', [
      Yii::t('app-salmon2', 'Hazard Level'),
      $model->danger_rate !== null
        ? Yii::$app->formatter->asPercent(floor((float)$model->danger_rate) / 100, 0)
        : '?',
    ]),
  ),
  [
    'class' => [
      'omit',
      'simple-battle-rule',
    ],
  ],
);
