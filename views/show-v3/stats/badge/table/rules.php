<?php

declare(strict_types=1);

use app\assets\GameModeIconsAsset;
use app\models\Rule3;
use app\models\TricolorRole3;
use app\models\UserBadge3Rule;
use app\models\UserBadge3Tricolor;
use yii\helpers\ArrayHelper;
use yii\web\View;

/**
 * @var Rule3[] $rules
 * @var TricolorRole3[] $roles
 * @var View $this
 * @var array<string, UserBadge3Rule> $badgeRules
 * @var array<string, UserBadge3Tricolor> $badgeTricolor
 * @var array<string, int> $badgeAdjust
 * @var bool $isEditing
 */

$am = Yii::$app->assetManager;
$icon = GameModeIconsAsset::register($this);

echo $this->render('includes/group-header', ['label' => Yii::t('app', 'Mode')]);
foreach ($rules as $rule) {
  $key = 'rule-' . $rule->key;
  echo $this->render('includes/row', [
    'isEditing' => $isEditing,
    'itemKey' => $key,
    'icon' => $am->getAssetUrl($icon, sprintf('spl3/%s.png', $rule->key)),
    'label' => Yii::t('app-rule3', $rule->name),
    'value' => ArrayHelper::getValue($badgeRules, [$rule->key, 'count']),
    'adjust' => (int)ArrayHelper::getValue($badgeAdjust, $key, 0),
    'badgePath' => 'rules/' . $rule->key,
    'steps' => match ($rule->key) {
      'nawabari' => [
        [   0,   50, 0, 1],
        [  50,  250, 1, 2],
        [ 250, 1200, 2, 3],
        [1200, null, 3, 3],
      ],
      default => [
        [   0,  100, 0, 1],
        [ 100, 1000, 1, 2],
        [1000, null, 2, 2],
      ],
    },
  ]);
}
foreach ($roles as $role) {
  $key = 'rule-tricolor-' . $role->key;
  echo $this->render('includes/row', [
    'isEditing' => $isEditing,
    'itemKey' => $key,
    'icon' => $am->getAssetUrl($icon, sprintf('spl3/tricolor-%s.png', $role->key)),
    'label' => vsprintf('%s - %s', [
      Yii::t('app-rule3', 'Tricolor Turf War'),
      Yii::t('app-rule3', $role->name),
    ]),
    'value' => ArrayHelper::getValue($badgeTricolor, [$role->key, 'count']),
    'adjust' => (int)ArrayHelper::getValue($badgeAdjust, $key, 0),
    'badgePath' => 'rules/tricolor-' . $role->key,
    'steps' => [
      [0, 1, 0, 1],
      [1, 10, 1, 2],
      [10, null, 2, 2],
    ],
  ]);
}
