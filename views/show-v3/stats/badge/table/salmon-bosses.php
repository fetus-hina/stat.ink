<?php

declare(strict_types=1);

use app\assets\Spl3SalmonidAsset;
use app\models\SalmonBoss3;
use app\models\UserBadge3BossSalmonid;
use yii\helpers\ArrayHelper;
use yii\web\View;

/**
 * @var SalmonBoss3[] $bosses
 * @var View $this
 * @var array<string, UserBadge3BossSalmonid> $badgeBosses
 * @var array<string, int> $badgeAdjust
 * @var bool $isEditing
 */

$am = Yii::$app->assetManager;
$icon = Spl3SalmonidAsset::register($this);

echo $this->render('includes/group-header', ['label' => Yii::t('app-salmon3', 'Boss Salmonid')]);
foreach ($bosses as $boss) {
  $key = 'salmon-boss-' . $boss->key;
  echo $this->render('includes/row', [
    'isEditing' => $isEditing,
    'itemKey' => $key,
    'icon' => $am->getAssetUrl($icon, sprintf('%s.png', $boss->key)),
    'label' => Yii::t('app-salmon-boss3', $boss->name),
    'value' => ArrayHelper::getValue($badgeBosses, [$boss->key, 'count']),
    'adjust' => (int)ArrayHelper::getValue($badgeAdjust, $key, 0),
    'badgePath' => 'salmonids/' . $boss->key,
    'steps' => [
      [    0,   100, 0, 1],
      [  100,  1000, 1, 2],
      [ 1000, 10000, 2, 3],
      [10000,  null, 3, 3],
    ],
  ]);
}
