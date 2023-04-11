<?php

declare(strict_types=1);

use app\assets\Spl3WeaponAsset;
use app\models\Special3;
use app\models\UserBadge3Special;
use yii\helpers\ArrayHelper;
use yii\web\View;

/**
 * @var Special3[] $specials
 * @var TricolorRole3[] $roles
 * @var View $this
 * @var array<string, UserBadge3Rule> $badgeRules
 * @var array<string, UserBadge3Tricolor> $badgeTricolor
 * @var array<string, int> $badgeAdjust
 * @var bool $isEditing
 */

$am = Yii::$app->assetManager;
$icon = Spl3WeaponAsset::register($this);

echo $this->render('includes/group-header', ['label' => Yii::t('app', 'Special')]);
foreach ($specials as $special) {
  $key = 'special-' . $special->key;
  echo $this->render('includes/row', [
    'isEditing' => $isEditing,
    'itemKey' => $key,
    'icon' => $am->getAssetUrl($icon, sprintf('special/%s.png', $special->key)),
    'label' => Yii::t('app-special3', $special->name),
    'value' => ArrayHelper::getValue($badgeSpecials, [$special->key, 'count']),
    'adjust' => (int)ArrayHelper::getValue($badgeAdjust, $key, 0),
    'badgePath' => 'specials/' . $special->key,
    'steps' => [
      [   0,   30, 0, 1],
      [  30,  180, 1, 2],
      [ 180, 1200, 2, 3],
      [1200, null, 3, 3],
    ],
  ]);
}
