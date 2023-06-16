<?php

declare(strict_types=1);

use app\components\helpers\OgpHelper;
use app\components\helpers\StandardError;
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
 * @var array<int, array{weapon_id: int, players: int, player_for_winpct: int, wins: int}> $data
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
      <table class="table table-striped table-sortable" style="table-layout:fixed">
        <thead>
          <tr>
            <?= Html::tag('th', Html::encode('#'), [
              'class' => 'text-center',
              'data' => [
                'sort' => 'int',
                'data-sort-default' => 'asc',
              ],
              'style' => 'width:4em',
            ]) . "\n" ?>
            <?= Html::tag('th', Html::encode(Yii::t('app', 'Weapon')), [
              'class' => 'text-center',
              'data' => [
                'sort' => 'string',
                'data-sort-default' => 'asc',
              ],
              'style' => 'width:15em',
            ]) . "\n" ?>
            <?= Html::tag('th', Html::encode(Yii::t('app', 'Times')), [
              'class' => 'text-center',
              'data-sort' => 'int',
              'data-sort-default' => 'desc',
              'data-sort-onload' => 'yes',
              'style' => 'width:6em',
            ]) . "\n" ?>
            <?= Html::tag('th', Html::encode('%'), [
              'class' => 'text-center',
              'data-sort' => 'int',
              'data-sort-default' => 'desc',
              'style' => 'width:216px',
            ]) . "\n" ?>
            <?= Html::tag('th', Html::encode(Yii::t('app', 'Win %')), [
              'class' => 'text-center',
              'data-sort' => 'float',
              'data-sort-default' => 'desc',
              'style' => 'width:216px',
            ]) . "\n" ?>
            <th style="width:auto"></th>
          </tr>
        </thead>
        <tbody>
<?php $i = 0 ?>
<?php foreach ($weapons as $weaponId => $weapon) { ?>
<?php ++$i ?>
<?php $n = $data[$weaponId]['players'] ?? 0; ?>
<?php $winPlusLose = $data[$weaponId]['player_for_winpct'] ?? 0; ?>
<?php $win = $data[$weaponId]['wins'] ?? 0; ?>
          <tr>
            <?= Html::tag(
              'td',
              Html::encode($fmt->asInteger($i)),
              [
                'class' => 'text-center text-muted',
                'data-sort-value' => $i,
              ],
            ) . "\n" ?>
            <?= Html::tag(
              'td',
              Html::encode(Yii::t('app-weapon3', $weapon->name)),
              [
                'class' => 'nobr omit',
                'data-sort-value' => Yii::t('app-weapon3', $weapon->name),
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
              Progress::widget([
                'percent' => 100 * $n / $max,
                'label' => $fmt->asPercent($n / $total, 2),
                'barOptions' => [
                  'class' => 'progress-bar-info',
                ],
                'options' => [
                  'class' => ['progress-striped'],
                  'style' => [
                    'width' => '200px',
                  ],
                ],
              ]),
              [
                'data-sort-value' => $n,
              ],
            ) . "\n" ?>
            <?= Html::tag(
              'td',
              $winPlusLose > 0
                ? (function () use ($fmt, $win, $winPlusLose): string {
                  $errInfo = StandardError::winpct($win, $winPlusLose);
                  return Progress::widget([
                    'percent' => 100 * $win / $winPlusLose,
                    'label' =>  $fmt->asPercent($win / $winPlusLose, 1),
                    'barOptions' => [
                      'class' => 'auto-tooltip progress-bar-success',
                      'title' => $errInfo
                        ? Yii::t('app', '{from} - {to}', [
                          'from' => $fmt->asPercent($errInfo['min95ci'], 1),
                          'to' => $fmt->asPercent($errInfo['max95ci'], 1),
                        ])
                        : $fmt->asPercent($win / $winPlusLose, 1),
                    ],
                    'options' => [
                      'class' => [
                        'auto-tooltip progress-striped'
                      ],
                      'style' => [
                        'width' => '200px',
                      ],
                    ],
                  ]);
                })()
                : Html::tag('div', Html::encode(Yii::t('app', 'N/A')), [
                  'class' => 'text-center text-muted',
                ]),
              [
                'data-sort-value' => $winPlusLose > 0 ? $win / $winPlusLose : -1,
              ],
            ) . "\n" ?>
            <td></td>
          </tr>
<?php } ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
