<?php
declare(strict_types=1);

use app\components\i18n\Formatter;
use app\components\widgets\PlayerName2Widget;
use app\models\SalmonBossAppearance2;
use app\models\SalmonPlayer2;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;

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
$playerKillCount = function (SalmonPlayer2 $player, SalmonBossAppearance2 $bossInfo): string {
  foreach ($player->bossKills as $bossKill) {
    if ($bossKill->boss_id == $bossInfo->boss_id) {
      return Html::encode(
        Yii::t('app', '{number, plural, =1{1 kill} other{# kills}}', ['number' => $bossKill->count])
      );
    }
  }
  return Html::encode(
    Yii::t('app', '{number, plural, =1{1 kill} other{# kills}}', ['number' => 0])
  );
};

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
    'class' => 'table table-striped table-bordered',
  ],
  'columns' => array_filter([
    [
      'label' => Yii::t('app-salmon2', 'Boss Salmonid'),
      'format' => 'raw',
      'value' => function (SalmonBossAppearance2 $model): ?string {
        return $model->boss
          ? Html::tag('b', Html::encode(Yii::t('app-salmon-boss2', $model->boss->name)))
          : null;
      },
    ],
    [
      'attribute' => 'count',
      'label' => Yii::t('app-salmon2', 'Appearances'),
      'format' => 'integer',
    ],
    count($players) >= 1
      ? [
        'encodeLabel' => false,
        'label' => PlayerName2Widget::widget([
          'player' => $players[0],
          'user' => $model->user,
          'nameOnly' => true,
        ]),
        'value' => function (SalmonBossAppearance2 $model) use ($players, $playerKillCount): ?string {
          return $playerKillCount($players[0], $model);
        },
      ]
      : null,
    count($players) >= 2
      ? [
        'encodeLabel' => false,
        'label' => PlayerName2Widget::widget([
          'player' => $players[1],
          'user' => $model->user,
          'nameOnly' => true,
        ]),
        'value' => function (SalmonBossAppearance2 $model) use ($players, $playerKillCount): ?string {
          return $playerKillCount($players[1], $model);
        },
      ]
      : null,
    count($players) >= 3
      ? [
        'encodeLabel' => false,
        'label' => PlayerName2Widget::widget([
          'player' => $players[2],
          'user' => $model->user,
          'nameOnly' => true,
        ]),
        'value' => function (SalmonBossAppearance2 $model) use ($players, $playerKillCount): ?string {
          return $playerKillCount($players[2], $model);
        },
      ]
      : null,
    count($players) >= 4
      ? [
        'encodeLabel' => false,
        'label' => PlayerName2Widget::widget([
          'player' => $players[3],
          'user' => $model->user,
          'nameOnly' => true,
        ]),
        'value' => function (SalmonBossAppearance2 $model) use ($players, $playerKillCount): ?string {
          return $playerKillCount($players[3], $model);
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
