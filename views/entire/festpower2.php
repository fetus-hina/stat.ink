<?php
declare(strict_types=1);

use app\assets\TableResponsiveForceAsset;
use app\components\widgets\AdWidget;
use app\components\widgets\Alert;
use app\components\widgets\SnsWidget;
use yii\data\ArrayDataProvider;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\widgets\ActiveForm;

TableResponsiveForceAsset::register($this);

$title = Yii::t('app', 'Splatfest Power vs Win %');
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

  <?= Alert::widget([
    'options' => [
      'class' => 'alert-info',
    ],
    'body' => Yii::t(
      'app',
      '"mistaken": On {date}, <a href="{url}" class="alert-link">Nintendo misconfigure the matching server and ran the fest.</a>',
      [
        'url' => 'https://twitter.com/splatoonjp/status/998369650986569728',
        'date' => Yii::$app->formatter->asDate('2018-05-19', 'long'),
      ]
    ),
  ]) . "\n" ?>
  <?= GridView::widget([
    'dataProvider' => Yii::createObject([
      'class' => ArrayDataProvider::class,
      'sort' => false,
      'pagination' => false,
      'allModels' => $data,
    ]),
    'options' => [
      'class' => 'grid-view table-responsive table-responsive-force',
    ],
    'tableOptions' => [
      'class' => 'table table-striped table-hover',
    ],
    'layout' => '{items}',
    'columns' => [
      [
        'label' => Yii::t('app', 'Power Diff'),
        'attribute' => 'diff',
        'format' => 'integer',
        'contentOptions' => [
          'class' => 'text-right',
        ],
      ],
      [
        'label' => Yii::t('app', 'Battles (all)'),
        'contentOptions' => [
          'class' => 'text-right',
        ],
        'value' => function (array $row) use ($totalBattles): string {
          return vsprintf('%s (%s)', [
            Yii::$app->formatter->asInteger($row['battles']),
            $totalBattles > 0
              ? Yii::$app->formatter->asPercent($row['battles'] / $totalBattles, 2)
              : Yii::t('app', 'N/A'),
          ]);
        },
      ],
      [
        'label' => Yii::t('app', 'Greater Win % (all)'),
        'contentOptions' => [
          'class' => 'text-right',
        ],
        'format' => ['percent', 2],
        'value' => function (array $row): ?float {
          if ($row['diff'] === 0) {
            return 0.5;
          }
          if ($row['battles'] < 1) {
            return null;
          }
          return $row['higher_wins'] / $row['battles'];
        },
      ],
      [
        'label' => Yii::t('app', 'Battles (normal)'),
        'contentOptions' => [
          'class' => 'text-right',
        ],
        'value' => function (array $row) use ($totalBattles, $totalMistakeBattles): string {
          $total = $totalBattles - $totalMistakeBattles;
          $battles = $row['battles'] - $row['mistake_battles'];
          return vsprintf('%s (%s)', [
            Yii::$app->formatter->asInteger($battles),
            $total > 0
              ? Yii::$app->formatter->asPercent($battles / $total, 2)
              : Yii::t('app', 'N/A'),
          ]);
        },
      ],
      [
        'label' => Yii::t('app', 'Greater Win % (normal)'),
        'contentOptions' => [
          'class' => 'text-right',
        ],
        'format' => ['percent', 2],
        'value' => function (array $row): ?float {
          if ($row['diff'] === 0) {
            return 0.5;
          }
          $win = $row['higher_wins'] - $row['mistake_higher_wins'];
          $battles = $row['battles'] - $row['mistake_battles'];
          if ($battles < 1) {
            return null;
          }
          return $win / $battles;
        },
      ],
      [
        'label' => Yii::t('app', 'Battles (mistaken)'),
        'contentOptions' => [
          'class' => 'text-right',
        ],
        'value' => function (array $row) use ($totalMistakeBattles): string {
          return vsprintf('%s (%s)', [
            Yii::$app->formatter->asInteger($row['mistake_battles']),
            $totalMistakeBattles > 0
              ? Yii::$app->formatter->asPercent($row['mistake_battles'] / $totalMistakeBattles, 2)
              : Yii::t('app', 'N/A'),
          ]);
        },
      ],
      [
        'label' => Yii::t('app', 'Greater Win % (mistaken)'),
        'contentOptions' => [
          'class' => 'text-right',
        ],
        'format' => ['percent', 2],
        'value' => function (array $row): ?float {
          if ($row['diff'] === 0) {
            return 0.5;
          }
          if ($row['mistake_battles'] < 1) {
            return null;
          }
          return $row['mistake_higher_wins'] / $row['mistake_battles'];
        },
      ],
    ],
  ]) . "\n" ?>
</div>
