<?php

/**
 * @copyright Copyright (C) 2022-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

use app\components\widgets\Icon;
use app\models\BigrunMap3;
use app\models\SalmonMap3;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var BigrunMap3|SalmonMap3 $map
 * @var View $this
 */

$am = Yii::$app->assetManager;

echo Html::tag(
  'h3',
  implode(' ', [
    $map instanceof SalmonMap3 ? Icon::s3SalmonStage($map) : Icon::s3BigRun(),
    Html::encode(Yii::t('app-map3', $map->name)),
  ]),
  [
    'class' => 'my-2',
    'id' => sprintf('event-%s', $map->key),
  ],
);
