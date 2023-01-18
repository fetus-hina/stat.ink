<?php

declare(strict_types=1);

use app\components\widgets\AdWidget;
use app\components\widgets\Icon;
use app\components\widgets\SnsWidget;
use app\models\Lobby3;
use app\models\Rule3;
use app\models\Season3;
use app\models\Special3;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var Season3 $season
 * @var Season3[] $seasons
 * @var Special3 $special
 * @var View $this
 * @var array<int, Rule3> $rules
 * @var array<int, Special3> $specials
 * @var array<int, array<int, array{battles: int, wins: int}>> $data
 * @var array<string, Lobby3> $lobbies
 * @var callable(Season3): string $seasonUrl
 * @var callable(Special3): string $specialUrl
 */

$title = vsprintf('%s - %s', [
  Yii::t('app', 'Special Uses'),
  Yii::t('app-special3', $special->name),
]);
$this->title = Yii::$app->name . ' | ' . $title;

$this->registerMetaTag(['name' => 'twitter:card', 'content' => 'summary']);
$this->registerMetaTag(['name' => 'twitter:title', 'content' => $title]);
$this->registerMetaTag(['name' => 'twitter:description', 'content' => $title]);
$this->registerMetaTag(['name' => 'twitter:site', 'content' => '@stat_ink']);

?>
<div class="container">
  <h1><?= Html::encode($title) ?></h1>
  <?= AdWidget::widget() . "\n" ?>
  <?= SnsWidget::widget() . "\n" ?>

  <div class="mb-1">
    <?= $this->render('includes/season-selector', compact('season', 'seasons', 'seasonUrl')) . "\n" ?>
  </div>
  <div class="mb-3">
    <?= $this->render('includes/special-selector', compact('special', 'specials', 'specialUrl')) . "\n" ?>
  </div>

  <div class="mb-3">
    <?= Html::a(
      implode(' ', [
        Icon::back(),
        Html::encode(Yii::t('app', 'Back')),
      ]),
      ['entire/special-use3', 'season' => $season->id],
      ['class' => 'btn btn-default'],
    ) . "\n" ?>
  </div>

  <?= $this->render('includes/rule-link', ['rules' => array_values($rules)]) . "\n" ?>
  <?= $this->render('includes/aggregate2', compact('lobbies')) . "\n" ?>
  <?= $this->render('includes/error-bars-ci-95-99') . "\n" ?>

<?php foreach ($rules as $rule) { ?>
  <?= $this->render('special-use3-per-special/rule', [
    'data' => $data[$rule->id] ?? [],
    'rule' => $rule,
    'special' => $special,
  ]) . "\n" ?>
<?php } ?>
</div>
