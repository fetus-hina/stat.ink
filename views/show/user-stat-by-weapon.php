<?php

declare(strict_types=1);

use app\components\widgets\AdWidget;
use app\components\widgets\BattleFilterWidget;
use app\components\widgets\FA;
use app\components\widgets\SnsWidget;
use app\models\User;
use statink\yii2\sortableTable\SortableTableAsset;
use yii\data\ArrayDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;

/**
 * @var User $user
 * @var View $this
 */

$this->context->layout = 'main';
$title = Yii::t('app', '{name}\'s Battle Stats (by Weapon)', ['name' => $user->name]);
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
          'allModels' => $list,
          'sort' => false,
          'pagination' => false,
        ]),
        'columns' => [
          [
            'label' => Yii::t('app', 'Weapon'),
            'headerOptions' => [
              'data-sort' => 'string',
            ],
            'value' => function (array $row): string {
              return Yii::t('app-weapon', $row['weapon_name']);
            },
            'contentOptions' => function (array $row): array {
              return [
                'data-sort-value' => Yii::t('app-weapon', $row['weapon_name']),
              ];
            },
          ],
          [
            'encodeLabel' => false,
            'label' => implode(' ', [
              Html::encode(Yii::t('app', 'Battles')),
              Html::tag('span', '', ['class' => 'fas fa-angle-down arrow']),
            ]),
            'headerOptions' => [
              'data-sort' => 'int',
            ],
            'format' => 'raw',
            'value' => function (array $row) use ($filter): string {
              $params = array_merge($filter->toQueryParams(), ['show/user']);
              if (!isset($params['filter'])) {
                $params['filter'] = [];
              }
              $params['filter']['weapon'] = $row['weapon_key'];
              return Html::a(
                Html::encode(Yii::$app->formatter->asInteger($row['battles'])),
                $params
              );
            },
            'contentOptions' => function (array $row): array {
              return [
                'data-sort-value' => (string)(int)$row['battles'],
                'class' => 'text-right',
              ];
            },
          ],
          [
            'label' => Yii::t('app', 'Win %'),
            'headerOptions' => [
              'data-sort' => 'float',
            ],
            'format' => ['percent', 2],
            'value' => function (array $row): float {
              return $row['battles_win'] / $row['battles'];
            },
            'contentOptions' => function (array $row): array {
              return [
                'class' => 'text-right',
                'data-sort-value' => (string)($row['battles_win'] / $row['battles']),
              ];
            },
          ],
          [
            'label' => Yii::t('app', 'Avg Kills'),
            'headerOptions' => [
              'data-sort' => 'float',
            ],
            'format' => ['decimal', 2],
            'value' => function (array $row): ?float {
              if ($row['kd_available'] < 1) {
                return null;
              }
              return $row['kills'] / $row['kd_available'];
            },
            'contentOptions' => function (array $row): array {
              return [
                'class' => 'text-right',
                'data-sort-value' => ($row['kd_available'] < 1)
                  ? '-1'
                  : (string)($row['kills'] / $row['kd_available']),
              ];
            },
          ],
          [
            'label' => Yii::t('app', 'Avg Deaths'),
            'headerOptions' => [
              'data-sort' => 'float',
            ],
            'format' => ['decimal', 2],
            'value' => function (array $row): ?float {
              if ($row['kd_available'] < 1) {
                return null;
              }
              return $row['deaths'] / $row['kd_available'];
            },
            'contentOptions' => function (array $row): array {
              return [
                'class' => 'text-right',
                'data-sort-value' => ($row['kd_available'] < 1)
                  ? '-1'
                  : (string)($row['deaths'] / $row['kd_available']),
              ];
            },
          ],
          [
            'label' => Yii::t('app', 'Avg KR'),
            'headerOptions' => [
              'data-sort' => 'float',
            ],
            'format' => ['decimal', 3],
            'value' => function (array $row): ?float {
              if ($row['kd_available'] < 1) {
                return null;
              }
              if ($row['deaths'] == 0) {
                return $row['kills'] == 0 ? 0.0 : 99.999;
              }
              return $row['kills'] / $row['deaths'];
            },
            'contentOptions' => function (array $row): array {
              return [
                'class' => 'text-right',
                'data-sort-value' => ($row['kd_available'] < 1)
                  ? '-1'
                  : ($row['deaths'] == 0
                    ? ($row['kills'] == 0 ? '0.0' : '99.9999')
                    : ((string)($row['kills'] / $row['deaths']))
                  ),
              ];
            },
          ],
        ],
        'emptyText' => Yii::t('app', 'There are no data.'),
      ]) . "\n" ?>
    </div>
    <div class="col-xs-12 col-sm-4 col-md-4 col-lg-3">
      <?= BattleFilterWidget::widget([
        'route' => 'show/user-stat-by-weapon',
        'screen_name' => $user->screen_name,
        'filter' => $filter,
        'action' => 'summarize',
        'weapon' => false,
        'result' => false,
      ]) . "\n" ?>
      <?= $this->render('//includes/user-miniinfo', ['user' => $user]) . "\n" ?>
      <?= AdWidget::widget() . "\n" ?>
    </div>
  </div>
</div>
