<?php

declare(strict_types=1);

use app\components\helpers\OgpHelper;
use app\components\widgets\AdWidget;
use app\components\widgets\SnsWidget;
use app\components\widgets\v3\WeaponName;
use app\models\SalmonSchedule3;
use app\models\SalmonWeapon3;
use yii\bootstrap\Progress;
use yii\data\ArrayDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;

/**
 * @var SalmonSchedule3 $schedule
 * @var SalmonSchedule3[] $schedules
 * @var View $this
 * @var array<string, SalmonWeapon3> $weapons
 * @var array<string, int> $counts
 * @var int $max
 * @var int $total
 */

$this->context->layout = 'main';

$title = vsprintf('%s - %s', [
  Yii::t('app-salmon3', 'Salmon Run'),
  Yii::t('app-salmon3', 'Random Loan Rate'),
]);
$this->title = vsprintf('%s | %s', [
  $title,
  Yii::$app->name,
]);

OgpHelper::default($this, description: $title);

$dataProvider = Yii::createObject([
  'class' => ArrayDataProvider::class,
  'allModels' => array_map(
    fn (string $key, int $count): array => [
      'count' => $count,
      'key' => $key,
      'weapon' => $weapons[$key] ?? null,
    ],
    array_keys($counts),
    array_values($counts),
  ),
  'pagination' => false,
  'sort' => false,
]);

?>
<div class="container">
  <h1><?= Html::encode($title) ?></h1>

  <?= AdWidget::widget() . "\n" ?>
  <?= SnsWidget::widget() . "\n" ?>

  <?= Html::tag(
    'select',
    implode(
      '',
      array_map(
        fn (SalmonSchedule3 $model): string => Html::tag(
          'option',
          Html::encode(vsprintf('%s - %s', [
            Yii::$app->formatter->asDateTime($model->start_at, 'short', 'short'),
            Yii::$app->formatter->asDateTime($model->end_at, 'short', 'short'),
          ])),
          [
            'selected' => $model->id === $schedule->id,
            'value' => Url::to(
              ['entire/salmon3-random-loan', 'id' => $model->id],
              true,
            ),
          ],
        ),
        $schedules,
      ),
    ),
    [
      'class' => 'form-control',
      'onchange' => 'window.location.href = this.value',
    ],
  ) . "\n" ?>

  <?= Html::tag(
    'h2',
    vsprintf('%s - %s', [
      Yii::$app->formatter->asHtmlDateTime($schedule->start_at, 'short', 'short'),
      Yii::$app->formatter->asHtmlDateTime($schedule->end_at, 'short', 'short'),
    ])
  ) . "\n" ?>
<?php if ($schedule->map) { ?>
  <?= Html::tag('p', Html::encode(Yii::t('app-map3', $schedule->map->name))) . "\n" ?>
<?php } ?>

  <div class="table-responsive">
    <?= GridView::widget([
      'dataProvider' => $dataProvider,
      'layout' => '{items}',
      'tableOptions' => [
        'class' => 'table table-striped',
      ],
      'columns' => [
        [
          'label' => Yii::t('app', 'Weapon'),
          'headerOptions' => [
            'class' => 'text-center',
            'style' => [
              'width' => '15em',
            ],
          ],
          'contentOptions' => [
            'class' => 'nobr',
          ],
          'format' => 'raw',
          'value' => fn (array $model): string => WeaponName::widget([
            'model' => $model['weapon'],
            'showName' => true,
            'subInfo' => false,
          ]),
        ],
        [
          'label' => Yii::t('app', 'Times'),
          'headerOptions' => [
            'class' => 'text-center',
            'style' => [
              'width' => '6em',
            ],
          ],
          'contentOptions' => ['class' => 'text-right nobr'],
          'format' => 'integer',
          'attribute' => 'count',
        ],
        [
          'label' => '%',
          'headerOptions' => [
            'class' => 'text-center',
            'style' => [
              'width' => '6em',
            ],
          ],
          'contentOptions' => ['class' => 'text-right nobr'],
          'format' => ['percent', 2],
          'value' => fn (array $model): ?float => $total > 0
            ? $model['count'] / $total
            : null,
        ],
        [
          'label' => '',
          'headerOptions' => [
            'class' => 'text-center',
            'style' => [
              'min-width' => '200px',
            ],
          ],
          'contentOptions' => ['class' => 'text-left'],
          'format' => 'raw',
          'value' => fn (array $model): string => Progress::widget([
            'percent' => 100 * $model['count'] / $max,
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
        ],
      ],
      'beforeRow' => fn ($unused1, $unused2, int $index): ?string => $index === 0
        ? Html::tag('tr', implode('', [
          Html::tag('td', Html::encode(Yii::t('app', 'Total')), ['class' => 'text-center']),
          Html::tag('td', Yii::$app->formatter->asInteger($total), ['class' => 'text-right nobr']),
          Html::tag('td', ''),
          Html::tag('td', ''),
        ]))
        : null,
    ]) . "\n" ?>
  </div>
</div>
