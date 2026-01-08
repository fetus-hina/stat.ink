<?php

/**
 * @copyright Copyright (C) 2019-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\widgets\AdWidget;
use app\components\widgets\BattleFilterWidget;
use app\components\widgets\SnsWidget;
use app\models\User;
use statink\yii2\sortableTable\SortableTableAsset;
use yii\data\ArrayDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var User $user
 * @var View $this
 */

$this->context->layout = 'main';

$title = Yii::t('app', '{name}\'s Battle Stats (vs. Weapon)', [
  'name' => $user->name,
]);
$this->title = implode(' | ', [
  Yii::$app->name,
  $title,
]);

$this->registerMetaTag(['name' => 'twitter:card', 'content' => 'summary']);
$this->registerMetaTag(['name' => 'twitter:title', 'content' => $title]);
$this->registerMetaTag(['name' => 'twitter:description', 'content' => $title]);
$this->registerMetaTag(['name' => 'twitter:site', 'content' => '@stat_ink']);
$this->registerMetaTag(['name' => 'twitter:image', 'content' => $user->iconUrl]);
if ($user->twitter != '') {
  $this->registerMetaTag(['name' => 'twitter:creator', 'content' => '@' . $user->twitter]);
}

SortableTableAsset::register($this);
?>
<div class="container">
  <h1><?= Html::encode($title) ?></h1>

  <?= SnsWidget::widget() . "\n" ?>

  <div class="row">
    <div class="col-xs-12 col-sm-8 col-md-8 col-lg-9">
      <?= GridView::widget([
        'layout' => '{items}',
        'options' => [
          'class' => 'table-responsive',
        ],
        'tableOptions' => [
          'class' => 'table table-striped table-sortable',
        ],
        'dataProvider' => Yii::createObject([
          'class' => ArrayDataProvider::class,
          'allModels' => $data,
          'sort' => false,
          'pagination' => false,
        ]),
        'emptyText' => Yii::t('app', 'There are no data.'),
        'columns' => [
          [
            'label' => Yii::t('app', 'Enemy Weapon'),
            'headerOptions' => [
              'data-sort' => 'string',
            ],
            'value' => function (array $row): string {
              return Yii::t('app-weapon', $row['weapon_name']);
            },
            'contentOptions' => function (array $row): array {
              $subSp = null;
              if (($row['sub_name'] ?? null) && ($row['special_name'] ?? null)) {
                $subSp = implode(' / ', [
                  Yii::t('app-subweapon', $row['sub_name']),
                  Yii::t('app-special', $row['special_name']),
                ]);
              }

              return [
                'data-sort-value' => Yii::t('app-weapon', $row['weapon_name']),
                'class' => [
                  $subSp ? 'auto-tooltip' : '',
                ],
                'title' => $subSp,
              ];
            },
          ],
          [
            'label' => Yii::t('app', 'Battles'),
            'headerOptions' => [
              'data-sort' => 'int',
            ],
            'attribute' => 'battles',
            'format' => 'integer',
            'contentOptions' => function (array $row): array {
              return [
                'data-sort-value' => (string)(int)($row['battles'] ?? -1),
                'class' => 'text-right',
              ];
            },
          ],
          [
            'encodeLabel' => false,
            'label' => implode(' ', [
              Html::encode(Yii::t('app', 'Win %')),
              Html::tag('span', '', ['class' => 'fas fa-angle-down arrow']),
            ]),
            'headerOptions' => [
              'data-sort' => 'float',
            ],
            'format' => ['percent', 2],
            'value' => function (array $row): ?float {
              if (($row['battles'] ?? 0) < 1) {
                return null;
              }

              return $row['win_pct'] / 100.0;
            },
            'contentOptions' => function (array $row): array {
              return [
                'class' => 'text-right',
                'data-sort-value' => ($row['battles'] ?? 0) < 1
                  ? -1
                  : (string)(float)$row['win_pct'],
              ];
            },
          ],
          [
            'label' => Yii::t('app', 'Deaths'),
            'headerOptions' => [
              'data-sort' => 'int',
            ],
            'attribute' => 'deaths',
            'format' => 'integer',
            'contentOptions' => function (array $row): array {
              return [
                'data-sort-value' => (string)(int)($row['deaths'] ?? -1),
                'class' => 'text-right',
              ];
            },
          ],
          [
            'label' => Yii::t('app', 'Deaths Per Battle'),
            'headerOptions' => [
              'data-sort' => 'float',
            ],
            'attribute' => 'deaths_per_game',
            'format' => ['decimal', 3],
            'contentOptions' => function (array $row): array {
              return [
                'data-sort-value' => (string)(float)($row['deaths_per_game'] ?? -1),
                'class' => 'text-right',
              ];
            },
          ],
        ],
      ]) . "\n" ?>
    </div>
    <div class="col-xs-12 col-sm-4 col-md-4 col-lg-3">
      <?= BattleFilterWidget::widget([
        'route' => 'show/user-stat-vs-weapon',
        'screen_name' => $user->screen_name,
        'filter' => $filter,
        'action' => 'summarize',
        'result' => false,
      ]) . "\n" ?>
      <?= $this->render("//includes/user-miniinfo", ["user" => $user]) . "\n" ?>
      <?= AdWidget::widget() . "\n" ?>
    </div>
  </div>
</div>
