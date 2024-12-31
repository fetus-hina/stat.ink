<?php

/**
 * @copyright Copyright (C) 2019-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\assets\UserStatReportAsset;
use app\components\widgets\AdWidget;
use app\components\widgets\Icon;
use app\components\widgets\SnsWidget;
use app\models\User;
use yii\data\ArrayDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var User $user
 * @var View $this
 */

$title = Yii::t('app', '{name}\'s Battle Report', ['name' => $user->name]);

$this->context->layout = 'main';
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

if ($next) {
  $this->registerLinkTag(['rel' => 'next', 'href' => $next]);
}

if ($prev) {
  $this->registerLinkTag(['rel' => 'prev', 'href' => $prev]);
}

UserStatReportAsset::register($this);
?>
<div class="container">
  <h1><?= Yii::t('app', '{name}\'s Battle Report', [
    'name' => Html::a(
      Html::encode($user->name),
      ['show/user', 'screen_name' => $user->screen_name],
    ),
  ]) ?></h1>

  <?= AdWidget::widget() . "\n" ?>
  <?= SnsWidget::widget() . "\n" ?>

<?php if ($next || $prev) { ?>
  <div class="row">
<?php if ($prev) { ?>
    <div class="col-xs-6"><?= Html::a(
      implode(' ', [
        Icon::prevPage(),
        Html::encode(Yii::t('app', 'Prev. Month')),
      ]),
      $prev,
      ['class' => 'btn btn-default']
    ) ?></div>
<?php } ?>
<?php if ($next) { ?>
    <div class="col-xs-6 pull-right text-right"><?= Html::a(
      implode(' ', [
        Html::encode(Yii::t('app', 'Next Month')),
        Icon::nextPage(),
      ]),
      $next,
      ['class' => 'btn btn-default']
    ) ?></div>
<?php } ?>
  </div>
<?php } ?>
<?php $lastDate = null ?>
  <?= GridView::widget([
    'layout' => '{items}',
    'options' => [
      'class' => 'table-responsive',
    ],
    'tableOptions' => [
      'class' => 'table table-striped table-condensed',
    ],
    'dataProvider' => Yii::createObject([
      'class' => ArrayDataProvider::class,
      'allModels' => $list,
      'sort' => false,
      'pagination' => false,
    ]),
    'emptyText' => Yii::t('app', 'There are no data.'),
    'columns' => [
      [
        'label' => '',
        'format' => 'raw',
        'value' => function (array $row) use ($user): string {
          return Html::a(
            Icon::search(),
            ['show/user',
              'screen_name' => $user->screen_name,
              'filter' => [
                'lobby' => $row['lobby_key'],
                'rule' => $row['rule_key'],
                'map' => $row['map_key'],
                'weapon' => $row['weapon_key'],
                'term' => 'term',
                'term_from' => $row['date'] . ' 00:00:00',
                'term_to' => $row['date'] . ' 23:59:59',
              ],
            ]
          );
        },
      ],
      [
        'label' => Yii::t('app', 'Lobby'),
        'attribute' => 'lobby_name',
      ],
      [
        'label' => Yii::t('app', 'Mode'),
        'attribute' => 'rule_name',
      ],
      [
        'label' => Yii::t('app', 'Stage'),
        'attribute' => 'map_name',
      ],
      [
        'label' => Yii::t('app', 'Weapon'),
        'attribute' => 'weapon_name',
      ],
      [
        'label' => Yii::t('app', 'Battles'),
        'attribute' => 'battles',
        'format' => 'integer',
        'contentOptions' => function (array $row): array {
          return [
            'class' => 'text-right',
          ];
        },
      ],
      [
        'label' => Yii::t('app', 'Win %'),
        'format' => ['percent', 1],
        'contentOptions' => function (array $row): array {
          return [
            'class' => 'text-right',
          ];
        },
        'value' => function (array $row): ?float {
          return $row['battles'] < 1 ? null : ($row['wins'] / $row['battles']);
        },
      ],
      [
        'label' => Yii::t('app', 'Kills'),
        'format' => ['decimal', 2],
        'contentOptions' => function (array $row): array {
          return [
            'class' => 'text-right',
          ];
        },
        'value' => function (array $row): ?float {
          return $row['battles'] < 1 ? null : ($row['kill'] / $row['battles']);
        },
      ],
      [
        'label' => Yii::t('app', 'Deaths'),
        'format' => ['decimal', 2],
        'contentOptions' => function (array $row): array {
          return [
            'class' => 'text-right',
          ];
        },
        'value' => function (array $row): ?float {
          return $row['battles'] < 1 ? null : ($row['death'] / $row['battles']);
        },
      ],
      [
        'label' => Yii::t('app', 'KR'),
        'format' => ['decimal', 3],
        'contentOptions' => function (array $row): array {
          return [
            'class' => 'text-right',
          ];
        },
        'value' => function (array $row): ?float {
          if ($row['death'] > 0) {
            return $row['kill'] / $row['death'];
          }
          return ($row['kill'] > 0) ? 99.999 : null;
        },
      ],
    ],
    'beforeRow' => function (
      array $row,
      $key,
      $index,
      GridView $widget
    ) use (&$lastDate): string {
      if ($row['date'] === $lastDate) {
        return '';
      }
      $lastDate = $row['date'];
      return Html::tag(
        'tr',
        Html::tag(
          'th',
          Html::encode(Yii::$app->formatter->asDate($row['date'], 'full')),
          [
            'id' => 'date-' . $row['date'],
            'colspan' => count($widget->columns),
          ]
        ),
        ['class' => 'row-date']
      );
    },
  ]) . "\n" ?>
</div>
