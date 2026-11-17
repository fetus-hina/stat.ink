<?php

/**
 * @copyright Copyright (C) 2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

use app\components\widgets\Icon;
use app\models\Lobby3;
use app\models\Map3;
use app\models\Rule3;
use app\models\Spl3YearMonthForm;
use app\models\User;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var Lobby3 $lobby
 * @var Rule3 $rule
 * @var Spl3YearMonthForm $form
 * @var User $user
 * @var View $this
 * @var array<string, int|bool> $stats
 * @var list<array{map: Map3, stats: array<string, int|bool>}> $maps
 */

?>
<?= Html::tag(
  'h4',
  implode(' ', array_filter([
    Icon::s3Rule($rule),
    Html::tag('span', Html::encode(Yii::t('app-rule3', $rule->short_name)), [
      'class' => 'auto-tooltip',
      'title' => Yii::t('app-rule3', $rule->name),
    ]),
  ])),
  ['class' => 'mt-0 mb-2 omit'],
) . "\n" ?>
<?= $this->render('win-pie', [
  'filter' => [
    'lobby' => $lobby->key,
    'rule' => $rule->key,
  ],
  'form' => $form,
  'stats' => $stats,
  'user' => $user,
]) . "\n" ?>
<?php foreach ($maps as $mapInfo) { ?>
<div class="row mt-2">
  <div class="col-xs-6 small pt-3">
    <div class="omit"><?= Html::tag(
      'span',
      Html::encode(Yii::t('app-map3', $mapInfo['map']->short_name)),
      [
        'class' => 'auto-tooltip',
        'title' => Yii::t('app-map3', $mapInfo['map']->name),
      ],
    ) ?></div>
    <?= $this->render('battles', [
      'align' => 'text-left',
      'battles' => (int)$mapInfo['stats']['battles'],
      'filter' => [
        'lobby' => $lobby->key,
        'rule' => $rule->key,
        'map' => $mapInfo['map']->key,
      ],
      'form' => $form,
      'user' => $user,
    ]) . "\n" ?>
  </div>
  <div class="col-xs-6">
    <?= $this->render('//show-v3/stats/map-rule/table/body/cell-data-chart', [
      'stats' => $mapInfo['stats'],
    ]) . "\n" ?>
  </div>
</div>
<?php } ?>
