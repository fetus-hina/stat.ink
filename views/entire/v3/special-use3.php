<?php

/**
 * @copyright Copyright (C) 2022-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\helpers\OgpHelper;
use app\components\widgets\AdWidget;
use app\components\widgets\SnsWidget;
use app\models\Lobby3;
use app\models\Rule3;
use app\models\Season3;
use app\models\StatSpecialUse3;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var Lobby3 $xMatch
 * @var Season3 $season
 * @var Season3[] $seasons
 * @var StatSpecialUse3[] $total
 * @var View $this
 * @var array<int, Rule3> $rules
 * @var array<int, Special3> $specials
 * @var array<int, StatSpecialUse3[]> $data
 * @var callable(Season3): string $seasonUrl
 * @var int|null $maxAvgUses
 */

$title = Yii::t('app', 'Special Uses');
$this->title = $title . ' | ' . Yii::$app->name;

OgpHelper::default($this, title: $this->title);

?>
<div class="container">
  <h1>
    <?= Html::encode($title) . "\n" ?>
  </h1>
  <?= AdWidget::widget() . "\n" ?>
  <?= SnsWidget::widget() . "\n" ?>

  <div class="mb-3">
    <?= $this->render('includes/season-selector', compact('season', 'seasons', 'seasonUrl')) . "\n" ?>
  </div>
  <?= $this->render('includes/aggregate', compact('xMatch')) . "\n" ?>
  <?= $this->render('includes/rule-link', ['rules' => array_values($rules)]) . "\n" ?>

  <?= $this->render('special-use3/summary', [
    'data' => $data,
    'maxAvgUses' => $maxAvgUses,
    'rules' => $rules,
    'season' => $season,
    'specials' => $specials,
    'total' => $total,
  ]) . "\n" ?>

  <?= $this->render('special-use3/table', [
    'data' => $total,
    'maxAvgUses' => $maxAvgUses,
    'rule' => null,
    'season' => $season,
    'specials' => $specials,
  ]) . "\n" ?>

<?php foreach ($rules as $ruleId => $rule) { ?>
  <?= $this->render('special-use3/table', [
    'data' => $data[$ruleId] ?? [],
    'maxAvgUses' => $maxAvgUses,
    'rule' => $rule,
    'season' => $season,
    'specials' => $specials,
  ]) . "\n" ?>

<?php } ?>
</div>
