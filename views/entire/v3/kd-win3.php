<?php

declare(strict_types=1);

use app\assets\TableResponsiveForceAsset;
use app\components\widgets\AdWidget;
use app\components\widgets\SnsWidget;
use app\components\widgets\kdWin\KDWinTable;
use app\components\widgets\kdWin\LegendWidget;
use app\models\KDWin3FilterForm;
use app\models\Lobby3;
use app\models\Rule3;
use app\models\Season3;
use yii\bootstrap\ActiveForm;
use yii\db\ActiveQuery;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var KDWin3FilterForm $filter
 * @var View $this
 * @var array<int, Rule3> $rules
 * @var array<int, array<int, array<int, array{battles: int, wins: int}>>> $data
 * @var array<string, Lobby3> $lobbies
 * @var array<string, Season3> $seasons
 */

$title = Yii::t('app', 'Winning Percentage based on K/D');
$this->title = Yii::$app->name . ' | ' . $title;

$this->registerMetaTag(['name' => 'twitter:card', 'content' => 'summary']);
$this->registerMetaTag(['name' => 'twitter:title', 'content' => $title]);
$this->registerMetaTag(['name' => 'twitter:description', 'content' => $title]);
$this->registerMetaTag(['name' => 'twitter:site', 'content' => '@stat_ink']);

TableResponsiveForceAsset::register($this);

?>
<div class="container">
  <?= Html::tag('h1', Html::encode($title)) . "\n" ?>
  <p>
    <?= Html::encode(Yii::t(
      'app',
      'This website has color-blind support. Please check "Color-Blind Support" in the "Username/Guest" menu of the navbar to enable it.'
    )) . "\n" ?>
  </p>
  <?= AdWidget::widget() . "\n" ?>
  <?= SnsWidget::widget() . "\n" ?>

  <?= $this->render('kd-win3/version-tabs') . "\n" ?>
  <?= $this->render('kd-win3/filter', compact('filter', 'lobbies', 'seasons')) . "\n" ?>
  <?= $this->render('includes/rule-link', compact('rules')) . "\n" ?>

  <?= LegendWidget::widget() . "\n" ?>

<?php foreach ($rules as $rule) { ?>
  <?= $this->render('kd-win3/rule', [
    'data' => ArrayHelper::getValue($data, $rule->id) ?: [],
    'lobbyKey' => $filter->lobby,
    'rule' => $rule,
  ]) . "\n" ?>
<?php } ?>
</div>
