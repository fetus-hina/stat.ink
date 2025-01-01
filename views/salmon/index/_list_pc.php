<?php

/**
 * @copyright Copyright (C) 2018-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\assets\BattleListGroupHeaderAsset;
use app\assets\SalmonWorkListAsset;
use app\assets\Spl2WeaponAsset;
use app\components\grid\SalmonActionColumn;
use app\components\helpers\Battle as BattleHelper;
use app\components\i18n\Formatter;
use app\components\widgets\FA;
use app\components\widgets\Icon;
use app\components\widgets\Label;
use app\models\Salmon2;
use app\models\SalmonSchedule2;
use app\models\SalmonWeapon2;
use app\models\Weapon2;
use yii\grid\Column;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ListView;

/**
 * @var View $this
 */

SalmonWorkListAsset::register($this);

?>
<div class="text-center">
  <?= ListView::widget([
    'dataProvider' => $dataProvider,
    'itemOptions' => [ 'tag' => false ],
    'layout' => '{pager}',
    'pager' => [
      'maxButtonCount' => 5
    ],
  ]) . "\n" ?>
</div>
<?= $this->render('_summary', [
    'summary' => $dataProvider->query->summary(),
]) . "\n" ?>
<p>
  <?= Html::a(
    implode(' ', [
      (string)FA::fas('cogs')->fw(),
      Html::encode(Yii::t('app', 'View Settings')),
    ]),
    '#table-config',
    ['class' => 'btn btn-default']
  ) . "\n" ?>
  <?= Html::a(
    implode(' ', [
      (string)FA::fas('list')->fw(),
      Html::encode(Yii::t('app', 'Simplified List')),
    ]),
    array_merge(
      [], // $filter->toQueryParams(),
      ['salmon/index',
        'screen_name' => $user->screen_name,
        'v' => 'simple',
      ]
    ),
    ['class' => 'btn btn-default', 'rel' => 'nofollow']
  ) . "\n" ?>
</p>
<?= GridView::widget([
  'options' => [
    'id' => 'battles',
    'class' => 'table-responsive',
  ],
  'layout' => '{items}',
  'dataProvider' => $dataProvider,
  'formatter' => [
    'class' => Formatter::class,
    'nullDisplay' => '',
  ],
  'tableOptions' => ['class' => 'table table-striped table-condensed'],
  'rowOptions' => function (Salmon2 $model): array {
    return [
      'class' => [
        'battle-row',
      ],
    ];
  },
  'columns' => [
    [
      'class' => SalmonActionColumn::class,
      'user' => $user,
    ],
    [
      'headerOptions' => ['class' => 'cell-splatnet'],
      'contentOptions' => ['class' => 'cell-splatnet'],
      'attribute' => 'splatnet_number',
      'label' => '#',
      'format' => 'integer',
    ],
    [
      'attribute' => 'stage_id',
      'headerOptions' => ['class' => 'cell-map'],
      'contentOptions' => ['class' => 'cell-map'],
      'label' => Yii::t('app', 'Stage'),
      'value' => function (Salmon2 $model): ?string {
        return $model->stage_id
          ? Yii::t('app-salmon-map2', $model->stage->name)
          : null;
      },
    ],
    [
      'attribute' => 'stage_id',
      'headerOptions' => ['class' => 'cell-map-short'],
      'contentOptions' => ['class' => 'cell-map-short'],
      'label' => Yii::t('app', 'Stage'),
      'value' => function (Salmon2 $model): ?string {
        return $model->stage_id
          ? Yii::t('app-salmon-map2', $model->stage->short_name)
          : null;
      },
    ],
    [
      'attribute' => 'myData.special_id',
      'headerOptions' => ['class' => 'cell-special'],
      'contentOptions' => ['class' => 'cell-special'],
      'label' => Yii::t('app', 'Special'),
      'value' => function (Salmon2 $model): ?string {
        if (!$myData = $model->myData) {
          return null;
        }
        if (!$special = $myData->special) {
          return null;
        }
        return Yii::t('app-special2', $special->name);
      },
    ],
    [
      'label' => Yii::t('app', 'Result'),
      'headerOptions' => ['class' => 'cell-result'],
      'contentOptions' => ['class' => 'cell-result nobr'],
      'format' => 'raw',
      'value' => function (Salmon2 $model, $key, $index, Column $column): ?string {
        $isCleared = $model->getIsCleared();
        if ($isCleared === null) {
          return null;
        } elseif ($isCleared) {
          return Label::widget([
            'color' => 'success',
            'content' => Yii::t('app-salmon2', 'Cleared'),
          ]);
        } else {
          return implode(' ', [
            Label::widget([
              'color' => 'danger',
              'content' => Yii::t('app-salmon2', 'Failed in wave {waveNumber}', [
                'waveNumber' => $column->grid->formatter->asInteger($model->clear_waves + 1),
              ]),
            ]),
            $model->fail_reason_id
              ? Label::widget([
                'color' => $model->failReason->color,
                'content' => Yii::t('app-salmon2', $model->failReason->short_name),
                'options' => [
                  'class' => ['auto-tooltip'],
                  'title' => Yii::t('app-salmon2', $model->failReason->name),
                ],
              ])
              : '',
          ]);
        }
      },
    ],
    [
      'label' => Html::tag(
        'span',
        Html::encode(Yii::t('app-salmon2', 'Golden')),
        [
          'class' => 'auto-tooltip',
          'title' => Yii::t('app-salmon2', 'Golden Eggs'),
        ]
      ),
      'encodeLabel' => false,
      'headerOptions' => ['class' => 'cell-golden'],
      'contentOptions' => ['class' => 'cell-golden text-right'],
      'format' => 'integer',
      'attribute' => 'myData.golden_egg_delivered',
    ],
    [
      'label' => Html::tag(
        'span',
        Html::encode(Yii::t('app-salmon2', 'Golden/W')),
        [
          'class' => 'auto-tooltip',
          'title' => Yii::t('app-salmon2', 'Golden Eggs per Wave'),
        ]
      ),
      'encodeLabel' => false,
      'headerOptions' => ['class' => 'cell-golden-wave'],
      'contentOptions' => ['class' => 'cell-golden-wave text-right'],
      'format' => ['decimal', 1],
      'attribute' => 'goldenPerWave',
    ],
    [
      'label' => Html::tag(
        'span',
        Html::encode(Yii::t('app-salmon2', 'Ttl. Golden')),
        [
          'class' => 'auto-tooltip',
          'title' => Yii::t('app-salmon2', 'Team total Golden Eggs'),
        ]
      ),
      'encodeLabel' => false,
      'headerOptions' => ['class' => 'cell-golden-total'],
      'contentOptions' => ['class' => 'cell-golden-total text-right'],
      'format' => 'integer',
      'attribute' => 'teamTotalGoldenEggs',
    ],
    [
      'label' => Html::tag(
        'span',
        Html::encode(Yii::t('app-salmon2', 'Ttl. Golden (Wave)')),
        [
          'class' => 'auto-tooltip',
          'title' => Yii::t('app-salmon2', 'Team total Golden Eggs'),
        ]
      ),
      'encodeLabel' => false,
      'headerOptions' => ['class' => 'cell-golden-total-wave'],
      'contentOptions' => ['class' => 'cell-golden-total-wave'],
      'format' => 'raw',
      'value' => function ($model): ?string {
        $waves = $model->teamTotalGoldenEggsPerWave;
        if ($waves === null) {
          return null;
        }
        return implode(' - ', array_map(
          function (?\stdClass $wave): string {
            if ($wave === null) {
              return '?';
            }

            $f = Yii::$app->formatter;
            if ($wave->quota === null) {
              return Html::encode($f->asInteger($wave->delivered));
            }

            return Html::tag(
              'span',
              Html::encode($f->asInteger($wave->delivered)),
              [
                'class' => 'auto-tooltip',
                'title' => vsprintf('%s / %s', [
                  $f->asInteger($wave->delivered),
                  $f->asInteger($wave->quota),
                ]),
              ]
            );
          },
          $waves
        ));
      },
    ],
    [
      'label' => Html::tag(
        'span',
        Html::encode(Yii::t('app-salmon2', 'Pwr Eggs')),
        [
          'class' => 'auto-tooltip',
          'title' => Yii::t('app-salmon2', 'Power Eggs'),
        ]
      ),
      'encodeLabel' => false,
      'headerOptions' => ['class' => 'cell-power'],
      'contentOptions' => ['class' => 'cell-power text-right'],
      'format' => 'integer',
      'attribute' => 'myData.power_egg_collected',
    ],
    [
      'label' => Html::tag(
        'span',
        Html::encode(Yii::t('app-salmon2', 'Pwr E/W')),
        [
          'class' => 'auto-tooltip',
          'title' => Yii::t('app-salmon2', 'Power Eggs per Wave'),
        ]
      ),
      'encodeLabel' => false,
      'headerOptions' => ['class' => 'cell-power-wave'],
      'contentOptions' => ['class' => 'cell-power-wave text-right'],
      'format' => ['decimal', 1],
      'attribute' => 'pwrEggsPerWave',
    ],
    [
      'label' => Html::tag(
        'span',
        Html::encode(Yii::t('app-salmon2', 'Ttl. Pwr. E.')),
        [
          'class' => 'auto-tooltip',
          'title' => Yii::t('app-salmon2', 'Team total Power Eggs'),
        ]
      ),
      'encodeLabel' => false,
      'headerOptions' => ['class' => 'cell-power-total'],
      'contentOptions' => ['class' => 'cell-power-total text-right'],
      'format' => 'integer',
      'attribute' => 'teamTotalPowerEggs',
    ],
    [
      'label' => Html::tag(
        'span',
        Html::encode(Yii::t('app-salmon2', 'Ttl. Pwr. E. (Wave)')),
        [
          'class' => 'auto-tooltip',
          'title' => Yii::t('app-salmon2', 'Team total Power Eggs per Wave'),
        ]
      ),
      'encodeLabel' => false,
      'headerOptions' => ['class' => 'cell-power-total-wave'],
      'contentOptions' => ['class' => 'cell-power-total-wave'],
      'value' => function ($model): ?string {
        $waves = $model->teamTotalPowerEggsPerWave;
        if ($waves === null) {
          return null;
        }
        return implode(' - ', array_map(
          function (?int $wave): string {
            if ($wave === null) {
              return '?';
            }

            return Yii::$app->formatter->asInteger($wave);
          },
          $waves
        ));
      },
    ],
    [
      'label' => Yii::t('app-salmon2', 'Rescues'),
      'headerOptions' => ['class' => 'cell-rescue'],
      'contentOptions' => ['class' => 'cell-rescue text-right'],
      'format' => 'integer',
      'attribute' => 'myData.rescue',
    ],
    [
      'label' => Yii::t('app-salmon2', 'Deaths'),
      'headerOptions' => ['class' => 'cell-death'],
      'contentOptions' => ['class' => 'cell-death text-right'],
      'format' => 'integer',
      'attribute' => 'myData.death',
    ],
    [
      'label' => Yii::t('app-salmon2', 'Hazard Level'),
      'headerOptions' => ['class' => 'cell-danger-rate'],
      'contentOptions' => function (Salmon2 $model, $key, $index, Column $column): array {
        return [
          'class' => array_filter([
            'cell-danger-rate',
            ($model->danger_rate === null ? null : 'danger-rate-bg'),
            'text-center',
          ]),
          'data' => [
            'danger-rate' => $model->danger_rate,
          ],
        ];
      },
      'format' => ['decimal', 1],
      'attribute' => 'danger_rate',
    ],
    [
      'label' => Yii::t('app', 'Title'),
      'headerOptions' => ['class' => 'cell-title'],
      'contentOptions' => ['class' => 'cell-title'],
      'value' => function (Salmon2 $model): ?string {
        if (!$model->title_before_id) {
          return null;
        }

        return implode(' ', [
          Yii::t('app-salmon-title2', $model->titleBefore->name),
          $model->title_before_exp === null
            ? ''
            : Yii::$app->formatter->asInteger($model->title_before_exp),
        ]);
      },
    ],
    [
      'label' => Yii::t('app', 'Title (After)'),
      'headerOptions' => ['class' => 'cell-title-after'],
      'contentOptions' => ['class' => 'cell-title-after'],
      'value' => function (Salmon2 $model): ?string {
        if (!$model->title_after_id) {
          return null;
        }

        return implode(' ', [
          Yii::t('app-salmon-title2', $model->titleAfter->name),
          $model->title_after_exp === null
            ? ''
            : Yii::$app->formatter->asInteger($model->title_after_exp),
        ]);
      },
    ],
    [
      'label' => Yii::t('app', 'Date Time'),
      'headerOptions' => ['class' => 'cell-datetime'],
      'contentOptions' => ['class' => 'cell-datetime'],
      'attribute' => 'start_at',
      'format' => 'htmlDatetime',
    ],
    [
      'label' => Yii::t('app', 'Relative Time'),
      'headerOptions' => ['class' => 'cell-reltime'],
      'contentOptions' => ['class' => 'cell-reltime'],
      'attribute' => 'start_at',
      'format' => 'htmlRelative',
    ],
  ],
  'beforeRow' => function (
    Salmon2 $model,
    int $key,
    int $index,
    GridView $widget
  ) use ($user): ?string {
    static $lastPeriod = null;
    if ($lastPeriod !== $model->shift_period) {
      $lastPeriod = $model->shift_period;
      $fmt = Yii::$app->formatter;
      $from = $model->shift_period
        ? (BattleHelper::periodToRange2DT($model->shift_period)[0])
        : null;
      $shift = $from
        ? SalmonSchedule2::findOne(['start_at' => $from->format(DateTime::ATOM)])
        : null;
      BattleListGroupHeaderAsset::register($this);
      return Html::tag('tr', Html::tag(
        'td',
        (function () use ($fmt, $from, $shift, $user): string {
          if ($shift) {
            $weapons = ArrayHelper::getColumn(
              SalmonWeapon2::find()
                ->with(['weapon'])
                ->andWhere(['schedule_id' => $shift->id])
                ->orderBy(['id' => SORT_ASC])
                ->all(),
              'weapon',
            );

            $asset = $weapons ? Spl2WeaponAsset::register(Yii::$app->getView()) : null;

            return vsprintf('%s %s - %s (%s)', [
              Html::a(
                Icon::search(),
                ['salmon/index',
                  'screen_name' => $user->screen_name,
                  'filter' => ArrayHelper::merge(
                    (array)($_GET['filter'] ?? []),
                    ['filter' => sprintf('rotation:%d', $shift->period)],
                  ),
                ]
              ),
              $fmt->asHtmlDatetimeEx($from, 'medium', 'short'),
              $fmt->asHtmlDatetimeEx($shift->end_at, 'medium', 'short'),
              implode(' ', array_map(
                function (?Weapon2 $weapon) use ($asset): string {
                  if (!$weapon) {
                    return Html::tag('span', (string)FA::fas('question')->fw(), [
                      'class' => 'auto-tooltip',
                      'title' => Yii::t('app-salmon2', 'Random'),
                    ]);
                  }

                  return Html::img(
                    $asset->getIconUrl($weapon->key),
                    [
                      'style' => ['height' => '1.2em'],
                      'title' => Yii::t('app-weapon2', $weapon->name),
                      'class' => 'auto-tooltip',
                    ]
                  );
                },
                array_slice(
                  array_merge($weapons, [null, null, null, null]),
                  0,
                  4
                )
              )),
            ]);
          }

          if ($from) {
            return sprintf('%s -', $fmt->asHtmlDatetimeEx($from, 'medium', 'short'));
          }

          return Html::encode(Yii::t('app', 'Unknown'));
        })(),
        [
          'class' => 'battle-row-group-header',
          'colspan' => (string)count($widget->columns),
        ]
      ));
    }
    return null;
  },
]) . "\n" ?>
<div class="text-center">
  <?= ListView::widget([
    'dataProvider' => $dataProvider,
    'itemOptions' => [ 'tag' => false ],
    'layout' => '{pager}',
    'pager' => [
      'maxButtonCount' => 5
    ]
  ]) . "\n" ?>
</div>
