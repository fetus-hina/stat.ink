<?php

declare(strict_types=1);

use app\actions\show\v3\stats\BadgeAction;
use app\assets\s3PixelIcons\RuleIconAsset;
use app\models\Rule3;
use app\models\TricolorRole3;
use app\models\UserBadge3Rule;
use app\models\UserBadge3Tricolor;
use yii\helpers\ArrayHelper;
use yii\web\View;

/**
 * @var BadgeAction::ORDER_* $order
 * @var Rule3[] $rules
 * @var TricolorRole3[] $roles
 * @var View $this
 * @var array<string, UserBadge3Rule> $badgeRules
 * @var array<string, UserBadge3Tricolor> $badgeTricolor
 * @var array<string, int> $badgeAdjust
 * @var bool $isEditing
 */

$am = Yii::$app->assetManager;
$icon = RuleIconAsset::register($this);

// reorder $rules for display
if ($order === BadgeAction::ORDER_NUMBER) {
  usort(
    $rules,
    function (Rule3 $a, Rule3 $b) use ($badgeAdjust, $badgeRules): int {
      $aCount = array_sum([
        (int)ArrayHelper::getValue($badgeRules, [$a->key, 'count'], 0),
        (int)ArrayHelper::getValue($badgeAdjust, "rule-{$a->key}", 0),
      ]);
      $bCount = array_sum([
        (int)ArrayHelper::getValue($badgeRules, [$b->key, 'count'], 0),
        (int)ArrayHelper::getValue($badgeAdjust, "rule-{$b->key}", 0),
      ]);

      return $bCount <=> $aCount ?: $a->rank <=> $b->rank;
    },
  );

  usort(
    $roles,
    function (TricolorRole3 $a, TricolorRole3 $b) use ($badgeAdjust, $badgeTricolor): int {
      $aCount = array_sum([
        (int)ArrayHelper::getValue($badgeTricolor, [$a->key, 'count'], 0),
        (int)ArrayHelper::getValue($badgeAdjust, "rule-tricolor-{$a->key}", 0),
      ]);
      $bCount = array_sum([
        (int)ArrayHelper::getValue($badgeTricolor, [$b->key, 'count'], 0),
        (int)ArrayHelper::getValue($badgeAdjust, "rule-tricolor-{$b->key}", 0),
      ]);

      return $bCount <=> $aCount ?: $a->id <=> $b->id;
    },
  );
}

echo $this->render('includes/group-header', ['label' => Yii::t('app', 'Mode')]);
foreach ($rules as $rule) {
  $key = 'rule-' . $rule->key;
  echo $this->render('includes/row', [
    'isEditing' => $isEditing,
    'itemKey' => $key,
    'icon' => $am->getAssetUrl($icon, "{$rule->key}.png"),
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
    'icon' => $am->getAssetUrl($icon, sprintf('tricolor-%s.png', $role->key)),
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
