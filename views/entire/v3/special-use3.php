<?php

declare(strict_types=1);

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
 */

$title = Yii::t('app', 'Special Uses');
$this->title = Yii::$app->name . ' | ' . $title;

$this->registerMetaTag(['name' => 'twitter:card', 'content' => 'summary']);
$this->registerMetaTag(['name' => 'twitter:title', 'content' => $title]);
$this->registerMetaTag(['name' => 'twitter:description', 'content' => $title]);
$this->registerMetaTag(['name' => 'twitter:site', 'content' => '@stat_ink']);

?>
<div class="container">
  <h1>
    <?= Html::encode($title) . "\n" ?>
  </h1>
  <?= AdWidget::widget() . "\n" ?>
  <?= SnsWidget::widget() . "\n" ?>

  <?= $this->render('includes/season-selector', compact('season', 'seasons', 'seasonUrl')) . "\n" ?>
  <?= $this->render('includes/aggregate', compact('xMatch')) . "\n" ?>
  <?= $this->render('includes/rule-link', ['rules' => array_values($rules)]) . "\n" ?>

  <?= $this->render('special-use3/summary', [
    'data' => $data,
    'rules' => $rules,
    'specials' => $specials,
    'total' => $total,
  ]) . "\n" ?>

  <?= $this->render('special-use3/table', [
    'data' => $total,
    'rule' => null,
    'specials' => $specials,
  ]) . "\n" ?>

<?php foreach ($rules as $ruleId => $rule) { ?>
  <?= $this->render('special-use3/table', [
    'data' => $data[$ruleId] ?? [],
    'rule' => $rule,
    'specials' => $specials,
  ]) . "\n" ?>

<?php } ?>
</div>
