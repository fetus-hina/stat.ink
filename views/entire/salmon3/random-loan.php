<?php

declare(strict_types=1);

use app\components\helpers\DateTimeHelper;
use app\components\helpers\OgpHelper;
use app\components\widgets\AdWidget;
use app\components\widgets\SnsWidget;
use app\components\widgets\v3\WeaponName;
use app\models\SalmonSchedule3;
use app\models\SalmonScheduleWeapon3;
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

OgpHelper::default($this, title: $this->title);

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

$dropdownDatePattern = DateTimeHelper::formatDH();

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
        function (SalmonSchedule3 $model) use ($schedule): string {
          $isRareOnly = array_sum(
            array_map(
              fn (SalmonScheduleWeapon3 $info): int => $info->random?->key === 'random_rare' ? 1 : 0,
              $model->salmonScheduleWeapon3s,
            ),
          ) === 4;

          return Html::tag(
            'option',
            Html::encode(
              trim(
                implode(' ', [
                  $model->is_eggstra_work
                    ? sprintf('[%s]', Yii::t('app-salmon3', 'Eggstra Work'))
                    : '',
                  $model->big_map_id !== null
                    ? sprintf('[%s]', Yii::t('app-salmon3', 'Big Run'))
                    : '',
                  $isRareOnly
                    ? sprintf('[%s]', Yii::t('app-salmon3', 'Rare Only'))
                    : '',
                  Yii::t('app', '{from} - {to}', [
                    'from' => Yii::$app->formatter->asDate(
                      $model->start_at,
                      DateTimeHelper::formatYMDH(),
                    ),
                    'to' => Yii::$app->formatter->asDate(
                      $model->end_at,
                      DateTimeHelper::formatYMDH(),
                    ),
                  ]),
                ]),
              ),
            ),
            [
              'selected' => $model->id === $schedule->id,
              'value' => Url::to(
                ['entire/salmon3-random-loan', 'id' => $model->id],
                true,
              ),
            ],
          );
        },
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
    Html::encode(
      Yii::t('app', '{from} - {to}', [
        'from' => Yii::$app->formatter->asDate(
          $schedule->start_at,
          DateTimeHelper::formatYMDH(),
        ),
        'to' => Yii::$app->formatter->asDate(
          $schedule->end_at,
          DateTimeHelper::formatYMDH(),
        ),
      ]),
    )
  ) . "\n" ?>
  <?= Html::tag(
    'ul',
    implode(
      '',
      array_map(
        fn (string $html): string => Html::tag('li', $html),
        array_filter(
          array_merge(
            [
              $schedule->big_map_id !== null ? Html::encode(Yii::t('app-salmon3', 'Big Run')) : null,
              Html::encode(Yii::t('app-map3', $schedule->map?->name ?? $schedule->bigMap?->name ?? '?')),
            ],
            array_map(
              fn (SalmonScheduleWeapon3 $info): string => $info->weapon
                ? WeaponName::widget([
                  'model' => $info->weapon,
                  'showName' => true,
                  'subInfo' => false,
                ])
                : Html::encode(Yii::t('app-weapon3', $info->random?->name ?? '?')),
              $schedule->salmonScheduleWeapon3s,
            ),
          ),
        ),
      ),
    ),
    ['class' => 'mb-3'],
  ) . "\n" ?>

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
