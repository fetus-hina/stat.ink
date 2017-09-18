<?php
use app\assets\AppOptAsset;
use app\assets\JqueryStupidTableAsset;
use app\components\widgets\AdWidget;
use app\components\widgets\SnsWidget;
use jp3cki\yii2\flot\FlotAsset;
use jp3cki\yii2\flot\FlotStackAsset;
use jp3cki\yii2\flot\FlotTimeAsset;
use yii\data\ArrayDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Json;

$title = Yii::t('app', 'Weapons');
$this->title = Yii::$app->name . ' | ' . $title;

$this->registerMetaTag(['name' => 'twitter:card', 'content' => 'summary']);
$this->registerMetaTag(['name' => 'twitter:title', 'content' => $title]);
$this->registerMetaTag(['name' => 'twitter:description', 'content' => $title]);
$this->registerMetaTag(['name' => 'twitter:site', 'content' => '@stat_ink']);

FlotAsset::register($this);
FlotTimeAsset::register($this);
FlotStackAsset::register($this);
JqueryStupidTableAsset::register($this);

$asset = AppOptAsset::register($this);
$asset->registerJsFile($this, 'weapons.js');

$this->registerCss('.graph{height:300px}');
$this->registerJs(<<<'END_JS'
(function(){
  $('.table-sortable')
    .stupidtable()
    .on("aftertablesort",function(event,data){
      var th = $(this).find("th");
      th.find(".arrow").remove();
      var dir = $.fn.stupidtable.dir;
      var arrow = data.direction === dir.ASC ? "fa-angle-up" : "fa-angle-down";
      th.eq(data.column)
        .append(' ')
        .append(
          $('<span/>').addClass('arrow fa').addClass(arrow)
        );
    });
})();
END_JS
);
?>
<div class="container">
  <h1>
    <?= Html::encode($title) . "\n" ?>
  </h1>
  <?= AdWidget::widget() . "\n" ?>  
  <?= SnsWidget::widget() . "\n" ?>
  <ul class="nav nav-tabs">
    <li class="active"><a href="javascript:;">Splatoon 2</a></li>
    <li><?= Html::a('Splatoon', ['entire/weapons']) ?></li>
  </ul>
  <h2>
    <?= Html::encode(Yii::t('app', 'Weapons')) . "\n" ?>
  </h2>
  <p>
    <?= Html::encode(
      Yii::t(
        'app',
        'Excluded: The uploader, All players (Private Battle), Uploader\'s teammates (Squad Battle or Splatfest Battle)'
      )
    ) . "\n" ?>
  </p>
  <p>
    <?= Html::encode(
      Yii::t('app', '* This exclusion is in attempt to minimize overcounting in weapon usage statistics.')
    ) . "\n" ?>
  </p>
  <p>
    <?= implode(' | ', array_map(
      function ($row) : string {
        return Html::a(
          Html::encode($row->name),
          '#weapon-' . $row->key
        );
      },
      $entire
    )) . "\n" ?>
  </p>
  <h3 id="trends">
    <?= Html::encode(Yii::t('app', 'Trends')) . "\n" ?>
  </h3>
  <p>
    <?= Html::a(
      implode(' ', [
        Html::tag('span', '', ['class' => 'fa fa-exchange fa-fw']),
        Html::encode(Yii::t('app', 'Compare number of uses')),
      ]),
      ['entire/weapons2-use'],
      ['class' => 'btn btn-default', 'disabled' => true]
    ) . "\n" ?>
  </p>
  <div id="graph-trends-legends"></div>
  <?= Html::tag('div', '', [
    'id' => 'graph-trends',
    'class' => 'graph',
    'data' => [
      'label-others' => Yii::t('app', 'Others'),
    ],
  ]) . "\n" ?>
  <p class="text-right">
    <label>
      <input type="checkbox" id="stack-trends" value="1" checked>
      <?= Html::encode(Yii::t('app', 'Stack')) . "\n" ?>
    </label>
  </p>
  <?= Html::tag(
    'script',
    Json::encode($uses),
    ['id' => 'trends-json', 'type' => 'application/json']
  ) . "\n" ?>
<?php foreach ($entire as $rule) if ($rule->data->player_count > 0) { ?>
  <?= Html::tag(
    'h3',
    Html::encode($rule->name),
    ['id' => 'weapon-' . $rule->key]
  ) . "\n" ?>
  <p>
    <?= sprintf(
      '%s %s',
      Html::encode(Yii::t('app', 'Players:')),
      Html::encode(Yii::$app->formatter->asInteger($rule->data->player_count))
    ) . "\n" ?>
  </p>
  <div class="table-responsive table-responsive-force">
    <?= GridView::widget([
      // {{{
      'tableOptions' => ['class' => 'table table-striped table-condensed table-sortable'],
      'layout' => '{items}',
      'dataProvider' => new ArrayDataProvider([
        'allModels' => $rule->data->weapons,
        'pagination' => false,
        'sort' => false,
      ]),
      'columns' => [
        [
          'label' => Yii::t('app', 'Weapon'), // {{{
          'headerOptions' => [
            'data-sort' => 'string',
          ],
          'contentOptions' => function ($model) : array {
            return [
              'data-sort-value' => $model->name,
            ];
          },
          'format' => 'raw',
          'value' => function ($model) : string {
            return Html::tag(
              'span',
              Html::encode($model->name),
              [
                'class' => 'auto-tooltip',
                'title' => vsprintf('%s%s / %s%s', [
                  Yii::t('app', 'Sub:'),
                  $model->subweapon->name,
                  Yii::t('app', 'Special:'),
                  $model->special->name,
                ]),
              ]
            );
          },
          // }}}
        ],
        [
          'label' => Yii::t('app', 'Players'), // {{{
          'headerOptions' => [
            'data-sort' => 'int',
          ],
          'contentOptions' => function ($model) : array {
            return [
              'class' => 'text-right',
              'data-sort-value' => $model->count,
            ];
          },
          'format' => 'raw',
          'value' => function ($weapon) use ($rule) : string {
            return Html::tag(
              'span',
              Html::encode(Yii::$app->formatter->asInteger($weapon->count)),
              [
                'class' => 'auto-tooltip',
                'title' => Yii::$app->formatter->asPercent($weapon->count / $rule->data->player_count, 2),
              ]
            );
          },
          // }}}
        ],
        [
          'label' => Yii::t('app', 'Win %'), // {{{
          'headerOptions' => [
            'data-sort' => 'float',
          ],
          'contentOptions' => function ($model) : array {
            return [
              'class' => 'text-right',
              'data-sort-value' => $model->wp,
            ];
          },
          'format' => 'raw',
          'value' => function ($model) : string {
            return implode(' ', [
              Html::encode(Yii::$app->formatter->asPercent($model->wp / 100, 2)),
            ]);
          },
          // }}}
        ],
        [
          'label' => Yii::t('app', 'Avg Kills'), // {{{
          'headerOptions' => [
            'data-sort' => 'float',
          ],
          'contentOptions' => function ($model) : array {
            return [
              'class' => 'text-right',
              'data-sort-value' => $model->avg_kill,
            ];
          },
          'value' => function ($model) : string {
            return Yii::$app->formatter->asDecimal($model->avg_kill, 2);
          },
          // }}}
        ],
        [
          'label' => Yii::t('app', 'Avg Deaths'), // {{{
          'headerOptions' => [
            'data-sort' => 'float',
          ],
          'contentOptions' => function ($model) : array {
            return [
              'class' => 'text-right',
              'data-sort-value' => $model->avg_death,
            ];
          },
          'value' => function ($model) : string {
            return Yii::$app->formatter->asDecimal($model->avg_death, 2);
          },
          // }}}
        ],
        [
          'label' => Yii::t('app', 'Kill Ratio'), // {{{
          'headerOptions' => [
            'data-sort' => 'float',
          ],
          'contentOptions' => function ($model) : array {
            return [
              'class' => 'text-right',
              'data-sort-value' => $model->kill_ratio,
            ];
          },
          'format' => 'raw',
          'value' => function ($model) : string {
            if ($model->kill_ratio === null) {
              return '';
            }
            return implode(' ', [
              Html::encode(Yii::$app->formatter->asDecimal($model->kill_ratio, 3)),
              $this->render('/includes/kill_ratio_indicator', ['value' => $model->kill_ratio]),
            ]);
          },
          // }}}
        ],
        [
          'label' => Yii::t('app', 'Kills/min'), // {{{
          'headerOptions' => [
            'data-sort' => 'float',
          ],
          'contentOptions' => function ($model) : array {
            return [
              'class' => 'text-right',
              'data-sort-value' => $model->kill_per_min,
            ];
          },
          'value' => function ($model) : string {
            return Yii::$app->formatter->asDecimal($model->kill_per_min, 3);
          },
          // }}}
        ],
        [
          'label' => Yii::t('app', 'Deaths/min'), // {{{
          'headerOptions' => [
            'data-sort' => 'float',
          ],
          'contentOptions' => function ($model) : array {
            return [
              'class' => 'text-right',
              'data-sort-value' => $model->death_per_min,
            ];
          },
          'value' => function ($model) : string {
            return Yii::$app->formatter->asDecimal($model->death_per_min, 3);
          },
          // }}}
        ],
        [
          'label' => Yii::t('app', 'Avg Specials'), // {{{
          'headerOptions' => [
            'data-sort' => 'float',
          ],
          'contentOptions' => function ($model) : array {
            return [
              'class' => 'text-right',
              'data-sort-value' => $model->avg_special,
            ];
          },
          'value' => function ($model) : string {
            return Yii::$app->formatter->asDecimal($model->avg_special, 2);
          },
          // }}}
        ],
        [
          'label' => Yii::t('app', 'Specials/min'), // {{{
          'headerOptions' => [
            'data-sort' => 'float',
          ],
          'contentOptions' => function ($model) : array {
            return [
              'class' => 'text-right',
              'data-sort-value' => $model->special_per_min,
            ];
          },
          'value' => function ($model) : string {
            return Yii::$app->formatter->asDecimal($model->special_per_min, 3);
          },
          // }}}
        ],
        [
          'label' => Yii::t('app', 'Avg Inked'), // {{{
          'headerOptions' => [
            'data-sort' => 'float',
          ],
          'contentOptions' => function ($model) : array {
            return [
              'class' => 'text-right',
              'data-sort-value' => $model->avg_inked,
            ];
          },
          'value' => function ($model) : string {
            return Yii::$app->formatter->asDecimal($model->avg_inked, 1);
          },
          // }}}
        ],
        [
          'label' => Yii::t('app', 'Inked/min'), // {{{
          'headerOptions' => [
            'data-sort' => 'float',
          ],
          'contentOptions' => function ($model) : array {
            return [
              'class' => 'text-right',
              'data-sort-value' => $model->inked_per_min,
            ];
          },
          'value' => function ($model) : string {
            return Yii::$app->formatter->asDecimal($model->inked_per_min, 2);
          },
          // }}}
        ],
      ],
      // }}}
    ]) . "\n" ?>
  </div>
  <div class="table-responsive table-responsive-force">
    <?= GridView::widget([
      // {{{
      'tableOptions' => ['class' => 'table table-striped table-condensed table-sortable'],
      'layout' => '{items}',
      'dataProvider' => new ArrayDataProvider([
        'allModels' => $rule->special,
        'pagination' => false,
        'sort' => false,
      ]),
      'columns' => [
        [
          'label' => Yii::t('app', 'Special'), // {{{
          'attribute' => 'name',
          'headerOptions' => [
            'data-sort' => 'string',
          ],
          // }}}
        ],
        [
          'label' => Yii::t('app', 'Players'), // {{{
          'attribute' => 'count',
          'format' => 'integer',
          'headerOptions' => [
            'data-sort' => 'int',
          ],
          'contentOptions' => function ($model) use ($rule) {
            return [
              'class' => 'text-right auto-tooltip',
              'title' => Yii::$app->formatter->asPercent($model->count / $rule->data->player_count, 2),
              'data-sort-value' => $model->count,
            ];
          },
          // }}}
        ],
        [
          'label' => Yii::t('app', 'Win %'), // {{{
          'headerOptions' => [
            'data-sort' => 'float',
          ],
          'contentOptions' => function ($model) : array {
            return [
              'class' => 'text-right',
              'data-sort-value' => $model->wp,
            ];
          },
          'value' => function ($model) : string {
            return Yii::$app->formatter->asPercent($model->wp / 100, 2);
          },
          // }}}
        ],
        [
          'label' => Yii::t('app', 'Avg Kills'), // {{{
          'headerOptions' => [
            'data-sort' => 'float',
          ],
          'contentOptions' => function ($model) : array {
            return [
              'class' => 'text-right',
              'data-sort-value' => $model->avg_kill,
            ];
          },
          'value' => function ($model) : string {
            return Yii::$app->formatter->asDecimal($model->avg_kill, 2);
          },
          // }}}
        ],
        [
          'label' => Yii::t('app', 'Avg Deaths'), // {{{
          'headerOptions' => [
            'data-sort' => 'float',
          ],
          'contentOptions' => function ($model) : array {
            return [
              'class' => 'text-right',
              'data-sort-value' => $model->avg_death,
            ];
          },
          'value' => function ($model) : string {
            return Yii::$app->formatter->asDecimal($model->avg_death, 2);
          },
          // }}}
        ],
        [
          'label' => Yii::t('app', 'Kill Ratio'), // {{{
          'headerOptions' => [
            'data-sort' => 'float',
          ],
          'contentOptions' => function ($model) : array {
            return [
              'class' => 'text-right',
              'data-sort-value' => $model->kill_ratio,
            ];
          },
          'format' => 'raw',
          'value' => function ($model) : string {
            if ($model->kill_ratio === null) {
              return '';
            }
            return implode(' ', [
              Html::encode(Yii::$app->formatter->asDecimal($model->kill_ratio, 3)),
              $this->render('/includes/kill_ratio_indicator', ['value' => $model->kill_ratio]),
            ]);
          },
          // }}}
        ],
        [
          'label' => Yii::t('app', 'Avg Specials'), // {{{
          'headerOptions' => [
            'data-sort' => 'float',
          ],
          'contentOptions' => function ($model) : array {
            return [
              'class' => 'text-right',
              'data-sort-value' => $model->avg_special,
            ];
          },
          'value' => function ($model) : string {
            return Yii::$app->formatter->asDecimal($model->avg_special, 2);
          },
          // }}}
        ],
        [
          'label' => Yii::t('app', 'Encounter Ratio'), // {{{
          'headerOptions' => [
            'data-sort' => 'float',
          ],
          'contentOptions' => function ($model) : array {
            return [
              'class' => 'text-right',
              'data-sort-value' => $model->encounter_4,
            ];
          },
          'value' => function ($model) : string {
            return Yii::$app->formatter->asPercent($model->encounter_4 / 100, 2);
          },
          // }}}
        ],
      ],
      // }}}
    ]) . "\n" ?>
  </div>
  <div class="table-responsive table-responsive-force">
    <?= GridView::widget([
      // {{{
      'tableOptions' => ['class' => 'table table-striped table-condensed table-sortable'],
      'layout' => '{items}',
      'dataProvider' => new ArrayDataProvider([
        'allModels' => $rule->sub,
        'pagination' => false,
        'sort' => false,
      ]),
      'columns' => [
        [
          'label' => Yii::t('app', 'Sub Weapon'), // {{{
          'attribute' => 'name',
          'headerOptions' => [
            'data-sort' => 'string',
          ],
          // }}}
        ],
        [
          'label' => Yii::t('app', 'Players'), // {{{
          'attribute' => 'count',
          'format' => 'integer',
          'headerOptions' => [
            'data-sort' => 'int',
          ],
          'contentOptions' => function ($model) use ($rule) {
            return [
              'class' => 'text-right auto-tooltip',
              'title' => Yii::$app->formatter->asPercent($model->count / $rule->data->player_count, 2),
              'data-sort-value' => $model->count,
            ];
          },
          // }}}
        ],
        [
          'label' => Yii::t('app', 'Win %'), // {{{
          'headerOptions' => [
            'data-sort' => 'float',
          ],
          'contentOptions' => function ($model) : array {
            return [
              'class' => 'text-right',
              'data-sort-value' => $model->wp,
            ];
          },
          'value' => function ($model) : string {
            return Yii::$app->formatter->asPercent($model->wp / 100, 2);
          },
          // }}}
        ],
        [
          'label' => Yii::t('app', 'Avg Kills'), // {{{
          'headerOptions' => [
            'data-sort' => 'float',
          ],
          'contentOptions' => function ($model) : array {
            return [
              'class' => 'text-right',
              'data-sort-value' => $model->avg_kill,
            ];
          },
          'value' => function ($model) : string {
            return Yii::$app->formatter->asDecimal($model->avg_kill, 2);
          },
          // }}}
        ],
        [
          'label' => Yii::t('app', 'Avg Deaths'), // {{{
          'headerOptions' => [
            'data-sort' => 'float',
          ],
          'contentOptions' => function ($model) : array {
            return [
              'class' => 'text-right',
              'data-sort-value' => $model->avg_death,
            ];
          },
          'value' => function ($model) : string {
            return Yii::$app->formatter->asDecimal($model->avg_death, 2);
          },
          // }}}
        ],
        [
          'label' => Yii::t('app', 'Kill Ratio'), // {{{
          'headerOptions' => [
            'data-sort' => 'float',
          ],
          'contentOptions' => function ($model) : array {
            return [
              'class' => 'text-right',
              'data-sort-value' => $model->kill_ratio,
            ];
          },
          'format' => 'raw',
          'value' => function ($model) : string {
            if ($model->kill_ratio === null) {
              return '';
            }
            return implode(' ', [
              Html::encode(Yii::$app->formatter->asDecimal($model->kill_ratio, 3)),
              $this->render('/includes/kill_ratio_indicator', ['value' => $model->kill_ratio]),
            ]);
          },
          // }}}
        ],
        [
          'label' => Yii::t('app', 'Avg Specials'), // {{{
          'headerOptions' => [
            'data-sort' => 'float',
          ],
          'contentOptions' => function ($model) : array {
            return [
              'class' => 'text-right',
              'data-sort-value' => $model->avg_special,
            ];
          },
          'value' => function ($model) : string {
            return Yii::$app->formatter->asDecimal($model->avg_special, 2);
          },
          // }}}
        ],
        [
          'label' => Yii::t('app', 'Encounter Ratio'), // {{{
          'headerOptions' => [
            'data-sort' => 'float',
          ],
          'contentOptions' => function ($model) : array {
            return [
              'class' => 'text-right',
              'data-sort-value' => $model->encounter_4,
            ];
          },
          'value' => function ($model) : string {
            return Yii::$app->formatter->asPercent($model->encounter_4 / 100, 2);
          },
          // }}}
        ],
      ],
      // }}}
    ]) . "\n" ?>
  </div>
<?php } ?>
</div>
