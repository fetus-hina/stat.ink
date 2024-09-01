<?php

declare(strict_types=1);

use app\components\widgets\Icon;
use app\models\BigrunMap3;
use app\models\SalmonKing3;
use app\models\SalmonMap3;
use app\models\StatSalmon3MapKing;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var View $this
 * @var array<int, BigrunMap3> $bigMaps
 * @var array<int, SalmonKing3> $kings
 * @var array<int, SalmonMap3> $maps
 * @var array<int, StatSalmon3MapKing> $data
 */

$this->registerCss(
  implode('', [
    vsprintf('.graph-container{%s}', [
      Html::cssStyleFromArray([
        'min-width' => sprintf('%dpx', 220 * (count($kings) + 1)),
      ]),
    ]),
    vsprintf('.cell{%s}', [
      Html::cssStyleFromArray([
        'min-width' => '200px',
        'width' => sprintf('%f%%', 100.0 / (count($kings) + 1)),
      ]),
    ]),
  ]),
);

$results = ArrayHelper::index(
  $data,
  'king_id',
  fn (StatSalmon3MapKing $model): int => $model->map_id ?? (0x100 + $model->big_map_id),
);

?>
<div class="table-responsive table-responsive-force">
  <table class="table table-bordered table-striped table-condensed graph-container">
    <thead>
      <tr>
        <?= Html::tag(
          'th',
          Html::encode(Yii::t('app', 'Stage')),
          [
            'class' => 'text-center cell',
            'rowspan' => '2',
          ],
        ) . "\n" ?>
        <?= Html::tag(
          'th',
          Html::encode(Yii::t('app-salmon3', 'Boss Salmonids')),
          [
            'class' => 'text-center cell',
            'colspan' => count($kings),
          ],
        ) . "\n" ?>
      </tr>
      <tr>
<?php foreach ($kings as $king) { ?>
        <?= Html::tag(
          'th',
          implode(' ', [
            Icon::s3BossSalmonid($king),
            Html::tag(
              'span',
              Html::encode(Yii::t('app-salmon-boss3', $king->name)),
              ['class' => 'd-none d-md-inline'],
            ),
          ]),
          ['class' => 'text-center cell align-middle'],
        ) . "\n" ?>
<?php } ?>
    </thead>
    <tbody>
<?php foreach (array_merge($maps, $bigMaps) as $map) { ?>
      <tr>
        <?= Html::tag(
          'th',
          Html::tag(
            'div',
            implode('', [
              Html::tag(
                'div',
                match ($map::class) {
                  SalmonMap3::class => Icon::s3SalmonStage($map),
                  BigrunMap3::class => Icon::s3BigRun(),
                },
                ['style' => 'font-size:3em'],
              ),
              Html::tag(
                'div',
                Html::encode(Yii::t('app-map3', $map->short_name)),
                ['class' => 'd-md-none'],
              ),
              Html::tag(
                'div',
                Html::encode(Yii::t('app-map3', $map->name)),
                ['class' => 'd-none d-md-block'],
              ),
            ]),
          ),
          [
            'class' => 'cell align-middle text-center',
            'scope' => 'row',
          ],
        ) . "\n" ?>
<?php foreach ($kings as $king) { ?>
<?php $model = ArrayHelper::getValue($results, [
  $map instanceof SalmonMap3 ? $map->id : ($map->id + 0x100),
  $king->id,
]) ?>
        <?= $this->render('./table/cell', [
          'map' => $map,
          'king' => $king,
          'cleared' => $model?->cleared ?? 0,
          'jobs' => $model?->jobs ?? 0,
        ]) . "\n" ?>
<?php } ?>
      </tr>
<?php } ?>
    </tbody>
  </table>
</div>
