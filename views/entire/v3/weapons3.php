<?php

declare(strict_types=1);

use app\components\widgets\AdWidget;
use app\components\widgets\SnsWidget;
use app\models\Lobby3;
use app\models\Rule3;
use app\models\Season3;
use app\models\StatWeapon3Usage;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var Lobby3 $lobby
 * @var Rule3 $rule
 * @var Season3 $season
 * @var StatWeapon3Usage[] $data
 * @var View $this
 * @var array<int, Lobby3> $lobbies
 * @var array<int, Rule3> $rules
 * @var array<int, Season3> $seasons
 * @var callable(Lobby3): string $lobbyUrl
 * @var callable(Rule3): string $ruleUrl
 * @var callable(Season3): string $seasonUrl
 */

$title = Yii::t('app', 'Weapons');
$this->title = Yii::$app->name . ' | ' . $title;

$this->registerMetaTag(['name' => 'twitter:card', 'content' => 'summary']);
$this->registerMetaTag(['name' => 'twitter:title', 'content' => $title]);
$this->registerMetaTag(['name' => 'twitter:description', 'content' => $title]);
$this->registerMetaTag(['name' => 'twitter:site', 'content' => '@stat_ink']);

?>
<div class="container">
  <?= Html::tag('h1', Html::encode($title)) . "\n" ?>

  <?= AdWidget::widget() . "\n" ?>
  <?= SnsWidget::widget() . "\n" ?>

  <?= $this->render('weapons3/version-tabs') . "\n" ?>
  <div class="mb-3">
    <div class="mb-1">
      <?= $this->render('includes/season-selector', compact('season', 'seasons', 'seasonUrl')) . "\n" ?>
    </div>
    <?= $this->render('weapons3/lobby-tabs', compact('lobby', 'lobbies', 'lobbyUrl')) . "\n" ?>
    <?= $this->render('weapons3/rule-tabs', compact('rule', 'rules', 'ruleUrl')) . "\n" ?>
  </div>

  <?= $this->render('weapons3/summary', compact('data', 'rule')) . "\n" ?>
  <?= $this->render('weapons3/table', compact('data')) . "\n" ?>
</div>
