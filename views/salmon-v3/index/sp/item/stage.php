<?php

/**
 * @copyright Copyright (C) 2022-2026 AIZAWA Hina
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
  $model->stage
    ? Html::encode(Yii::t('app-map3', $model->stage->name))
    : ($model->bigStage
      ? Html::encode(Yii::t('app-map3', $model->bigStage->name))
      : '?'
    ),
  [
    'class' => [
      'simple-battle-rule',
      'omit',
    ],
  ],
);
