<?php

declare(strict_types=1);

use app\components\widgets\AdWidget;
use app\components\widgets\Icon;
use app\components\widgets\SnsWidget;
use yii\bootstrap\Progress;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var View $this
 * @var array<string, int> $votes
 */

$title = Yii::t('app', 'Splatfest Estimated Vote %');
$this->title = Yii::$app->name . ' | ' . $title;

$this->registerMetaTag(['name' => 'twitter:card', 'content' => 'summary']);
$this->registerMetaTag(['name' => 'twitter:title', 'content' => $title]);
$this->registerMetaTag(['name' => 'twitter:description', 'content' => $title]);
$this->registerMetaTag(['name' => 'twitter:site', 'content' => '@stat_ink']);

$this->registerCss(
    '.progress-bar-dark{background-color:#611eea}' .
    '.progress-bar-milk{background-color:#995934}' .
    '.progress-bar-white{background-color:#d6bf8e}'
);

?>
<div class="container">
  <?= Html::tag('h1', Html::encode($title)) . "\n" ?>

  <?= AdWidget::widget() . "\n" ?>
  <?= SnsWidget::widget() . "\n" ?>

  <p class="mb-1 small text-muted">
    <?= Html::encode(
      vsprintf('%s: %s', [
        Yii::t('app', 'Samples'),
        Yii::$app->formatter->asInteger(array_sum($votes)),
      ]),
    ) . "\n" ?>
  </p>
  <p class="mb-1 small text-muted">
    <?= Yii::t('app', 'Idea: {source}', [
      'source' => Html::a(
        vsprintf('%s %s', [
          Icon::twitter(),
          '@splatoon_weapon',
        ]),
        'https://twitter.com/splatoon_weapon/status/1612147667446157313',
      ),
    ]) . "\n" ?>
  </p>

  <div class="row">
    <div class="col-xs-12 mb-3" style="max-width:400px">
      <?= Progress::widget([
          'bars' => array_map(
              fn (string $key, int $count): array => [
                  'percent' => 100.0 * $count / array_sum($votes),
                  'label' => Yii::$app->formatter->asPercent($count / array_sum($votes), 0),
                  'options' => [
                      'class' => "progress-bar-{$key} auto-tooltip",
                      'title' => ucfirst($key),
                  ],
              ],
              array_keys($votes),
              array_values($votes),
          ),
      ]) . "\n" ?>
    </div>
  </div>
</div>
