<?php
declare(strict_types=1);

use app\assets\AppOptAsset;
use app\assets\RpgAwesomeAsset;
use app\components\grid\SalmonActionColumn;
use app\components\widgets\Label;
use app\models\Salmon2;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\i18n\Formatter;
use yii\widgets\ListView;

RpgAwesomeAsset::register($this);
AppOptAsset::register($this)->registerJsFile($this, 'salmon-work-list.js');
$this->registerJs('window.workList();');
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
  <a href="#table-config" class="btn btn-default">
    <span class="fa fa-cogs fa-fw"></span>
    <?= Html::encode(Yii::t('app', 'View Settings')) . "\n" ?>
  </a>
  <?= Html::a(
    '<span class="fa fa-list fa-fw"></span> ' . Html::encode(Yii::t('app', 'Simplified List')),
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
      'data' => [
        'period' => $model->shift_period,
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
      'value' => function (Salmon2 $model): ?string {
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
                'waveNumber' => Yii::$app->formatter->asInteger($model->clear_waves + 1),
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
      'contentOptions' => ['class' => 'cell-golden'],
      'format' => 'integer',
      'attribute' => 'myData.golden_egg_delivered',
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
      'contentOptions' => ['class' => 'cell-power'],
      'format' => 'integer',
      'attribute' => 'myData.power_egg_collected',
    ],
    [
      'label' => Yii::t('app-salmon2', 'Hazard Level'),
      'headerOptions' => ['class' => 'cell-danger-rate'],
      'contentOptions' => ['class' => 'cell-danger-rate'],
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
      'format' => 'raw',
      'value' => function (Salmon2 $model): ?string {
        return $model->start_at === null
          ? null
          : Html::tag(
            'time',
            Html::encode(Yii::$app->formatter->asDateTime($model->start_at, 'short')),
            ['datetime' => Yii::$app->formatter->asDateTime($model->start_at, 'yyyy-MM-dd\'T\'HH:mm:ssZZZZZ')]
          );
      },
    ],
    [
      // reltime {{{
      'label' => Yii::t('app', 'Relative Time'),
      'headerOptions' => ['class' => 'cell-reltime'],
      'contentOptions' => ['class' => 'cell-reltime'],
      'format' => 'raw',
      'value' => function (Salmon2 $model): ?string {
        return $model->start_at === null
          ? null
          : Html::tag(
            'time',
            Html::encode(Yii::$app->formatter->asRelativeTime($model->start_at)),
            [ 
              'datetime' => Yii::$app->formatter->asDateTime($model->start_at, 'yyyy-MM-dd\'T\'HH:mm:ssZZZZZ'),
              'class' => 'auto-tooltip',
              'title' => Yii::$app->formatter->asDateTime($model->start_at),
            ]
          );
      },
      // }}}
    ],
  ],
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
