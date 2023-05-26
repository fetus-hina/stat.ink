<?php

declare(strict_types=1);

use app\components\widgets\Icon;
use app\models\Map3;
use app\models\SalmonMap3;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var Map3|null $bigMap
 * @var SalmonMap3|null $map
 * @var View $this
 */

if ($map) {
  echo Html::tag(
    'h3',
    implode(' ', [
      Html::a(
        Icon::scrollTo(),
        sprintf('#event-%s', rawurlencode($map->key)),
      ),
      Html::encode(Yii::t('app-map3', $map->name)),
    ]),
    [
      'class' => [
        'my-2',
        'omit',
      ],
    ],
  );
} elseif ($bigMap) {
  $am = Yii::$app->assetManager;
  echo Html::tag(
    'h3',
    implode(' ', [
      Html::a(
        Icon::scrollTo(),
        sprintf('#event-%s', rawurlencode($bigMap->key)),
      ),
      Html::encode(Yii::t('app-map3', $bigMap->name)),
    ]),
    [
      'class' => [
        'my-2',
        'omit',
      ],
    ],
  );
} else {
  throw new LogicException();
}
