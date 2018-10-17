<?php
declare(strict_types=1);

use app\components\i18n\Formatter;
use app\models\SalmonBossAppearance2 ;
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

$widget = Yii::createObject([
  'class' => GridView::class,
  'dataProvider' => new ActiveDataProvider([
    'query' => $bosses,
    'pagination' => false,
    'sort' => false,
  ]),
  'formatter' => $formatter,
  'layout' => '{items}',
  'tableOptions' => [
    'class' => 'table table-striped',
  ],
  'columns' => [
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
  ],
]);

$this->registerCss(implode('', [
  "#{$widget->id} th:first-child{width:15em}",
  "@media(max-width:30em){#{$widget->id} th:first-child{width:auto}}",
]));
?>
<h2><?= Yii::t('app-salmon2', 'Boss Salmonids') ?></h2>
<?= $widget->run() ?>
