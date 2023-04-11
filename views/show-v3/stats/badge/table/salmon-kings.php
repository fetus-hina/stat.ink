<?php

declare(strict_types=1);

use app\assets\Spl3SalmonidAsset;
use app\models\SalmonKing3;
use app\models\UserBadge3KingSalmonid;
use yii\helpers\ArrayHelper;
use yii\web\View;

/**
 * @var SalmonKing3[] $kings
 * @var View $this
 * @var array<string, UserBadge3KingSalmonid> $badgeKings
 * @var array<string, int> $badgeAdjust
 * @var bool $isEditing
 */

$am = Yii::$app->assetManager;
$icon = Spl3SalmonidAsset::register($this);

echo $this->render('includes/group-header', ['label' => Yii::t('app-salmon3', 'King Salmonid')]);
foreach ($kings as $king) {
  $key = 'salmon-king-' . $king->key;
  echo $this->render('includes/row', [
    'isEditing' => $isEditing,
    'itemKey' => $key,
    'icon' => $am->getAssetUrl($icon, sprintf('%s.png', $king->key)),
    'label' => Yii::t('app-salmon-boss3', $king->name),
    'value' => ArrayHelper::getValue($badgeKings, [$king->key, 'count']),
    'adjust' => (int)ArrayHelper::getValue($badgeAdjust, $key, 0),
    'badgePath' => 'salmonids/' . $king->key,
    'steps' => [
      [   0,   10, 0, 1],
      [  10,  100, 1, 2],
      [ 100, 1000, 2, 3],
      [1000, null, 3, 3],
    ],
  ]);
}
