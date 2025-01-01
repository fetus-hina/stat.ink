<?php

/**
 * @copyright Copyright (C) 2018-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\i18n\Formatter;
use app\components\widgets\PlayerName2Widget;
use app\models\Salmon2;
use app\models\SalmonBossAppearance2;
use app\models\SalmonPlayer2;
use statink\yii2\sortableTable\SortableTableAsset;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var Salmon2 $model
 * @var View $this
 */

$formatter = Yii::createObject([
  'class' => Formatter::class,
  'nullDisplay' => '',
]);

$bosses = $model->getBossAppearances()
  ->andWhere(['>', 'count', 0])
  ->with('boss')
  ->orderBy([
    'count' => SORT_DESC,
    'boss_id' => SORT_DESC,
  ]);
if ($bosses->count() < 1) {
  return;
}

// 指定プレーヤー・オオモノシャケの組み合わせのキル数を取得する
$playerKillCount = function (SalmonPlayer2 $player, SalmonBossAppearance2 $bossInfo, bool $format = true): string {
  foreach ($player->bossKills as $bossKill) {
    if ($bossKill->boss_id == $bossInfo->boss_id) {
      return $format
        ? Html::encode(
          Yii::t('app', '{number, plural, =1{1 kill} other{# kills}}', ['number' => $bossKill->count])
        )
        : (string)(int)$bossKill->count;
    }
  }
  return $format
    ? Html::encode(
      Yii::t('app', '{number, plural, =1{1 kill} other{# kills}}', ['number' => 0])
    )
    : '0';
};

SortableTableAsset::register($this);

$players = $model->players;
$widget = Yii::createObject([
  'class' => GridView::class,
  'dataProvider' => new ActiveDataProvider([
    'query' => $bosses,
    'pagination' => false,
    'sort' => false,
  ]),
  'formatter' => $formatter,
  'layout' => '{items}',
  'options' => [
    'class' => 'table-responsive grid-view',
  ],
  'tableOptions' => [
    'class' => 'table table-striped table-bordered table-sortable',
  ],
  'columns' => array_filter([
    [
      'label' => Yii::t('app-salmon2', 'Boss Salmonid'),
      'headerOptions' => [
        'data-sort' => 'string',
      ],
      'format' => 'raw',
      'value' => function (SalmonBossAppearance2 $model): ?string {
        return $model->boss
          ? Html::tag('b', Html::encode(Yii::t('app-salmon-boss2', $model->boss->name)))
          : null;
      },
      'contentOptions' => function (SalmonBossAppearance2 $model): array {
        return [
          'data-sort-value' => Yii::t('app-salmon-boss2', $model->boss->name),
        ];
      },
    ],
    [
      'attribute' => 'count',
      'encodeLabel' => false,
      'label' => implode(' ', [
        Yii::t('app-salmon2', 'Appearances'),
        '<span class="arrow fa fa-angle-down"></span>',
      ]),
      'headerOptions' => [
        'data-sort' => 'int',
      ],
      'contentOptions' => function (SalmonBossAppearance2 $model): array {
        return [
          'data-sort-value' => (string)(int)$model->count,
        ];
      },
      'format' => 'integer',
    ],
    count($players) >= 1
      ? [
        'encodeLabel' => false,
        'label' => PlayerName2Widget::widget([
          'player' => $players[0],
          'user' => $model->user,
        ]),
        'headerOptions' => [
          'data-sort' => 'int',
        ],
        'value' => function (SalmonBossAppearance2 $model) use ($players, $playerKillCount): ?string {
          return $playerKillCount($players[0], $model);
        },
        'contentOptions' => function (SalmonBossAppearance2 $model) use ($players, $playerKillCount): array {
          return [
            'data-sort-value' => $playerKillCount($players[0], $model, false),
          ];
        },
      ]
      : null,
    count($players) >= 2
      ? [
        'encodeLabel' => false,
        'label' => PlayerName2Widget::widget([
          'player' => $players[1],
          'user' => $model->user,
        ]),
        'headerOptions' => [
          'data-sort' => 'int',
        ],
        'value' => function (SalmonBossAppearance2 $model) use ($players, $playerKillCount): ?string {
          return $playerKillCount($players[1], $model);
        },
        'contentOptions' => function (SalmonBossAppearance2 $model) use ($players, $playerKillCount): array {
          return [
            'data-sort-value' => $playerKillCount($players[1], $model, false),
          ];
        },
      ]
      : null,
    count($players) >= 3
      ? [
        'encodeLabel' => false,
        'headerOptions' => [
          'data-sort' => 'int',
        ],
        'label' => PlayerName2Widget::widget([
          'player' => $players[2],
          'user' => $model->user,
        ]),
        'value' => function (SalmonBossAppearance2 $model) use ($players, $playerKillCount): ?string {
          return $playerKillCount($players[2], $model);
        },
        'contentOptions' => function (SalmonBossAppearance2 $model) use ($players, $playerKillCount): array {
          return [
            'data-sort-value' => $playerKillCount($players[2], $model, false),
          ];
        },
      ]
      : null,
    count($players) >= 4
      ? [
        'encodeLabel' => false,
        'headerOptions' => [
          'data-sort' => 'int',
        ],
        'label' => PlayerName2Widget::widget([
          'player' => $players[3],
          'user' => $model->user,
        ]),
        'value' => function (SalmonBossAppearance2 $model) use ($players, $playerKillCount): ?string {
          return $playerKillCount($players[3], $model);
        },
        'contentOptions' => function (SalmonBossAppearance2 $model) use ($players, $playerKillCount): array {
          return [
            'data-sort-value' => $playerKillCount($players[3], $model, false),
          ];
        },
      ]
      : null,
  ]),
]);

$this->registerCss(implode('', [
  "#{$widget->id} th:first-child{width:15em}",
  "@media(max-width:30em){#{$widget->id} th:first-child{width:auto}}",
]));
?>
<h2><?= Yii::t('app-salmon2', 'Boss Salmonids') ?></h2>
<?= $widget->run() ?>
