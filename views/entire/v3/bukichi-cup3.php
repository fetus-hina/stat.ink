<?php

declare(strict_types=1);

use app\components\helpers\OgpHelper;
use app\components\widgets\AdWidget;
use app\components\widgets\SnsWidget;
use app\models\Event3;
use app\models\Weapon3;
use statink\yii2\sortableTable\SortableTableAsset;
use yii\bootstrap\Progress;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var Event3 $event
 * @var View $this
 * @var array<int, Weapon3> $weapons
 * @var array<int, array{weapon_id: int, players: int}> $data
 */

SortableTableAsset::register($this);

$title = vsprintf('%s - %s', [
  Yii::t('app', 'Loaned Weapons'),
  Yii::t('db/event3', $event->name),
]);
$this->title = $title . ' | ' . Yii::$app->name;

OgpHelper::default($this, title: $title);

$fmt = Yii::$app->formatter;

$total = array_sum(ArrayHelper::getColumn($data, 'players'));
$max = max(ArrayHelper::getColumn($data, 'players'));

?>
<div class="container">
  <?= Html::tag('h1', Html::encode($title)) . "\n" ?>

  <?= AdWidget::widget() . "\n" ?>
  <?= SnsWidget::widget() . "\n" ?>

  <div class="mb-3">
    <?= Html::tag(
      'p',
      Html::encode(
        vsprintf('%s: %s', [
          Yii::t('app', 'Samples'),
          $fmt->asInteger($total),
        ]),
      ),
      ['class' => 'mt-0 mb-1'],
    ) . "\n" ?>
    <?= Html::tag(
      'p',
      Html::encode(
        vsprintf('%s: %s / %s', [
          Yii::t('app', 'Number of weapons confirmed'),
          $fmt->asInteger(count($data)),
          $fmt->asInteger(count($weapons)),
        ]),
      ),
      ['class' => 'mt-0 mb-1'],
    ) . "\n" ?>
  </div>
  <div class="mb-3">
    <div class="table-responsive">
      <table class="table table-striped table-sortable">
        <thead>
          <tr>
            <?= Html::tag('th', Html::encode(Yii::t('app', 'Weapon')), [
              'class' => 'text-center',
              'data-sort' => 'int',
              'data-sort-default' => 'asc',
              'data-sort-onload' => 'yes',
              'style' => 'width:18em',
            ]) . "\n" ?>
            <?= Html::tag('th', Html::encode(Yii::t('app', 'Times')), [
              'class' => 'text-center',
              'data-sort' => 'int',
              'data-sort-default' => 'desc',
              'style' => 'width:6em',
            ]) . "\n" ?>
            <?= Html::tag('th', Html::encode('%'), [
              'class' => 'text-center',
              'data-sort' => 'int',
              'data-sort-default' => 'desc',
              'style' => 'width:6em',
            ]) . "\n" ?>
            <?= Html::tag('th', Html::encode(''), [
              'class' => 'text-center',
              'data-sort' => 'int',
              'data-sort-default' => 'desc',
              'style' => 'min-width:200px',
            ]) . "\n" ?>
          </tr>
        </thead>
        <tbody>
<?php $i = 0 ?>
<?php foreach ($weapons as $weaponId => $weapon) { ?>
<?php ++$i ?>
<?php $n = $data[$weaponId]['players'] ?? 0; ?>
          <tr>
            <?= Html::tag(
              'td',
              Html::encode(Yii::t('app-weapon3', $weapon->name)),
              [
                'data-sort-value' => $i,
              ],
            ) . "\n" ?>
            <?= Html::tag(
              'td',
              Html::encode($fmt->asInteger($n)),
              [
                'class' => 'text-right',
                'data-sort-value' => $n,
              ],
            ) . "\n" ?>
            <?= Html::tag(
              'td',
              Html::encode($fmt->asPercent($n / $total, 2)),
              [
                'class' => 'text-right',
                'data-sort-value' => $n,
              ],
            ) . "\n" ?>
            <?= Html::tag(
              'td',
              Progress::widget([
                'percent' => 100 * $n / $max,
                'barOptions' => [
                  'class' => 'progress-bar-info',
                ],
                'options' => [
                  'class' => ['progress-striped'],
                  'style' => [
                    'min-width' => '200px',
                    'max-width' => '500px',
                  ],
                ],
              ]),
              [
                'data-sort-value' => $n,
              ],
            ) . "\n" ?>
          </tr>
<?php } ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
