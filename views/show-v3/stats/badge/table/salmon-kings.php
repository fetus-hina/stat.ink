<?php

declare(strict_types=1);

use app\actions\show\v3\stats\BadgeAction;
use app\components\widgets\Icon;
use app\models\SalmonKing3;
use app\models\UserBadge3KingSalmonid;
use yii\helpers\ArrayHelper;
use yii\web\View;

/**
 * @var BadgeAction::ORDER_* $order
 * @var SalmonKing3[] $kings
 * @var View $this
 * @var array<string, UserBadge3KingSalmonid> $badgeKings
 * @var array<string, int> $badgeAdjust
 * @var bool $isEditing
 */

// reorder $kings for display
if ($order === BadgeAction::ORDER_NUMBER) {
  usort(
    $kings,
    function (SalmonKing3 $a, SalmonKing3 $b) use ($badgeAdjust, $badgeKings): int {
      $aCount = array_sum([
        (int)ArrayHelper::getValue($badgeKings, [$a->key, 'count'], 0),
        (int)ArrayHelper::getValue($badgeAdjust, "salmon-king-{$a->key}", 0),
      ]);
      $bCount = array_sum([
        (int)ArrayHelper::getValue($badgeKings, [$b->key, 'count'], 0),
        (int)ArrayHelper::getValue($badgeAdjust, "salmon-king-{$b->key}", 0),
      ]);

      return $bCount <=> $aCount ?: $a->id <=> $b->id;
    },
  );
}

echo $this->render('includes/group-header', ['label' => Yii::t('app-salmon3', 'King Salmonid')]);
foreach ($kings as $king) {
  $key = 'salmon-king-' . $king->key;
  echo $this->render('includes/row', [
    'isEditing' => $isEditing,
    'itemKey' => $key,
    'icon' => Icon::s3BossSalmonid($king, '2em'),
    'iconFormat' => 'raw',
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
