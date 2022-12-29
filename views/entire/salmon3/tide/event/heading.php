<?php

declare(strict_types=1);

use app\assets\GameModeIconsAsset;
use app\models\Map3;
use app\models\SalmonMap3;
use yii\helpers\Html;
use yii\web\AssetManager;
use yii\web\View;

/**
 * @var Map3|SalmonMap3 $map
 * @var View $this
 */

$am = Yii::$app->assetManager;
assert($am instanceof AssetManager);

/**
 * @var string|null $icon
 */
$icon = match (get_class($map)) {
  Map3::class => Html::img(
    $am->getAssetUrl(
      $am->getBundle(GameModeIconsAsset::class),
      'spl3/salmon-bigrun-36x36.png',
    ),
    [
      'draggable' => 'false',
      'class' => 'basic-icon',
      'style' => [
        '--icon-height' => '1em',
        '--icon-valign' => 'baseline',
      ],
    ],
  ),
  default => null,
};

echo Html::tag(
  'h3',
  implode(
    ' ',
    array_filter(
      [
        $icon,
        Html::encode(Yii::t('app-map3', $map->name)),
      ],
      fn (?string $v): bool => $v !== null,
    ),
  ),
  [
    'class' => 'my-2',
    'id' => sprintf('event-%s', $map->key),
  ],
);
