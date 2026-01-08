<?php

/**
 * @copyright Copyright (C) 2017-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\assets\EntireWeaponsAsset;
use app\assets\InlineListAsset;
use app\assets\Spl2WeaponAsset;
use app\assets\TableResponsiveForceAsset;
use app\components\widgets\AdWidget;
use app\components\widgets\FA;
use app\components\widgets\KillRatioBadgeWidget;
use app\components\widgets\SnsWidget;
use app\models\Map2;
use jp3cki\yii2\flot\FlotAsset;
use jp3cki\yii2\flot\FlotStackAsset;
use jp3cki\yii2\flot\FlotSymbolAsset;
use jp3cki\yii2\flot\FlotTimeAsset;
use statink\yii2\sortableTable\SortableTableAsset;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Nav;
use yii\data\ArrayDataProvider;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\web\View;

/**
 * @var View $this
 */

TableResponsiveForceAsset::register($this);

$title = Yii::t('app', 'Weapons');
$this->title = Yii::$app->name . ' | ' . $title;

$this->registerMetaTag(['name' => 'twitter:card', 'content' => 'summary']);
$this->registerMetaTag(['name' => 'twitter:title', 'content' => $title]);
$this->registerMetaTag(['name' => 'twitter:description', 'content' => $title]);
$this->registerMetaTag(['name' => 'twitter:site', 'content' => '@stat_ink']);

SortableTableAsset::register($this);
EntireWeaponsAsset::register($this);
?>
<div class="container">
  <h1>
    <?= Html::encode($title) . "\n" ?>
  </h1>

  <?= AdWidget::widget() . "\n" ?>  
  <?= SnsWidget::widget() . "\n" ?>

  <nav><?= Nav::widget([
    'options' => [
      'class' =>'nav-tabs',
    ],
    'items' => [
      [
        'label' => 'Splatoon 3',
        'url' => ['entire/weapons3'],
      ],
      [
        'label' => 'Splatoon 2',
        'url' => ['entire/weapons2'],
        'active' => true,
      ],
      [
        'label' => 'Splatoon',
        'url' => ['entire/weapons'],
      ],
    ],
  ]) ?></nav>

  <h2><?= Html::encode(Yii::t('app', 'Weapons')) ?></h2>
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
      Yii::t('app', '* This exclusion is an attempt to minimize overcounting in weapon usage statistics.')
    ) . "\n" ?>
  </p>

<?php InlineListAsset::register($this) ?>
  <nav>
    <ul class="inline-list mb-3"><?= implode('', array_map(
      function (stdClass $rule): string {
        return Html::tag(
          'li',
          Html::a(
            Html::encode($rule->name),
            sprintf('#weapon-%s', $rule->key),
          )
        );
      },
      array_filter(
        $entire,
        function (stdClass $rule): bool {
          return $rule->data->player_count > 0;
        }
      )
    )) ?></ul>
    <?= Html::a(
      Html::encode('Tier (Rank X, Solo queue) <alpha>'),
      ['entire/weapons2-tier'],
      ['class' => 'btn btn-default']
    ) . "\n" ?>
  </nav>

<?php if ($uses) { ?>
  <h3 id="trends">
    <?= Html::encode(Yii::t('app', 'Trends')) . "\n" ?>
  </h3>
  <p>
    <?= Html::a(
      implode(' ', [
        (string)FA::fas('exchange-alt')->fw(),
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
<?php } ?>
  <h3 id="stats">
    <?= Html::encode(Yii::t('app', 'Stats')) . "\n" ?>
  </h3>
  <?php $_form = ActiveForm::begin([
      'action' => ['entire/weapons2', '#' => 'stats'],
      'method' => 'get',
      'options' => [
        'id' => 'filter-form',
        'class' => 'form-inline',
        'style' => [
          'margin-top' => '20px',
        ],
      ],
      'enableClientValidation' => false,
    ]);
    echo "\n"
  ?>
    <?= $_form->field($form, 'term')
      ->label(false)
      ->dropDownList($form->getTermList(), [
        'onchange' => 'document.getElementById("filter-form").submit()',
      ]) . "\n" ?>
    <?= $_form->field($form, 'map')
      ->label(false)
      ->dropDownList(
        ArrayHelper::merge(
          ['' => Yii::t('app-map2', 'Any Stage')],
          Map2::getSortedMap()
        ),
        [
          'onchange' => 'document.getElementById("filter-form").submit()',
        ]
      ) . "\n" ?>
  <?php ActiveForm::end(); echo "\n"; ?>
<?php foreach ($entire as $rule) if ($rule->data->player_count > 0) { ?>
  <?= Html::tag(
    'h4',
    implode(' ', [
      Html::encode($rule->name),
    ]),
    ['id' => 'weapon-' . $rule->key]
  ) . "\n" ?>
  <p>
    <?= vsprintf('%s %s', [
      Html::encode(Yii::t('app', 'Players:')),
      Html::encode(Yii::$app->formatter->asInteger($rule->data->player_count)),
    ]) ?><br>
    <?= vsprintf('%s %s', [
      Html::encode(Yii::t('app', 'Systematic error of win %') . ':'),
      Html::encode(Yii::t('app', '{pct_point} percentage point', [
        'pct_point' => Yii::$app->formatter->asDecimal(
          (function () use ($rule): ?float {
            $totalBattles = array_sum(ArrayHelper::getColumn($rule->data->weapons, 'count'));
            if ($totalBattles < 1) {
              return null;
            }
            $totalWins = array_sum(ArrayHelper::getColumn($rule->data->weapons, 'win_count'));
            $rate = $totalWins / $totalBattles;
            return ($rate - 0.5) * 100;
          })(),
          2,
          [],
          [
            NumberFormatter::POSITIVE_PREFIX => '+',
            NumberFormatter::NEGATIVE_PREFIX => '-',
          ]
        ),
      ]))
    ]) ?>
  </p>
<?php
$maxWP = max(array_map(
  function ($model) : float {
    return (float)$model->wp;
  },
  $rule->data->weapons
));
?>
  <?= GridView::widget([
    // {{{
    'options' => ['class' => 'table-responsive table-responsive-force'],
    'tableOptions' => ['class' => 'table table-striped table-condensed table-sortable'],
    'layout' => '{items}',
    'dataProvider' => new ArrayDataProvider([
      'allModels' => $rule->data->weapons,
      'pagination' => false,
      'sort' => false,
    ]),
    'columns' => array_merge(
      [
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
          'value' => function ($model) use ($rule) : string {
            $asset = Spl2WeaponAsset::register($this);
            return Html::a(
              implode(' ', [
                Html::img($asset->getIconUrl($model->key), [
                  'class' => 'auto-tooltip',
                  'title' => $model->name,
                  'style' => [
                    'width' => 'auto',
                    'height' => '1.5em',
                  ],
                ]),
                Html::encode($model->name),
              ]),
              ['weapon2', 'weapon' => $model->key, 'rule' => $rule->key]
            );
          },
          // }}}
        ],
        [
          // Sub {{{
          'label' => Html::tag(
            'span',
            Html::encode(Yii::t('app', 'Sub Weapon')),
            ['class' => 'sr-only']
          ),
          'encodeLabel' => false,
          'format' => 'raw',
          'value' => function ($model): string {
            $asset = Spl2WeaponAsset::register($this);
            return Html::img($asset->getIconUrl('sub/' . $model->subweapon->key), [
              'class' => 'auto-tooltip',
              'title' => $model->subweapon->name,
              'style' => [
                'width' => 'auto',
                'height' => '1.25em',
              ],
            ]);
          },
          // }}}
        ],
        [
          // Special {{{
          'label' => Html::tag(
            'span',
            Html::encode(Yii::t('app', 'Special')),
            ['class' => 'sr-only']
          ),
          'encodeLabel' => false,
          'format' => 'raw',
          'value' => function ($model): string {
            $asset = Spl2WeaponAsset::register($this);
            return Html::img($asset->getIconUrl('sp/' . $model->special->key), [
              'class' => 'auto-tooltip',
              'title' => $model->special->name,
              'style' => [
                'width' => 'auto',
                'height' => '1.25em',
              ],
            ]);
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
          'value' => function ($model) use ($maxWP) : string {
            return Html::tag(
              'div',
              Html::tag(
                'div',
                Html::encode(Yii::$app->formatter->asPercent($model->wp / 100, 2)),
                [
                  'class' => 'progress-bar',
                  'style' => [
                    'width' => sprintf(
                      '%f%%',
                      ($maxWP > 0)
                        ? $model->wp / $maxWP * 100
                        : 0
                    ),
                  ],
                ]
              ),
              [
                'class' => 'progress',
                'style' => [
                  'min-width' => '100px',
                ],
              ]
            );
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
          'contentOptions' => function ($model): array {
            return [
              'class' => 'text-right',
              'data-sort-value' => $model->kill_ratio,
            ];
          },
          'format' => 'raw',
          'value' => function ($model): string {
            if ($model->kill_ratio === null) {
              return '';
            }

            return implode(' ', [
              Html::encode(Yii::$app->formatter->asDecimal($model->kill_ratio, 3)),
              KillRatioBadgeWidget::widget([
                'killRatio' => $model->kill_ratio,
              ]),
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
      ($rule->key === 'nawabari')
        ? [
          [
            'label' => Yii::t('app', 'Inking Performance'), // {{{
            'headerOptions' => [
              'data-sort' => 'float',
            ],
            'contentOptions' => function ($model) : array {
              return [
                'class' => 'text-right',
                'data-sort-value' => (float)$model->ink_performance,
              ];
            },
            'value' => function ($model) : string {
              return Yii::$app->formatter->asDecimal($model->ink_performance, 3);
            },
            // }}}
          ],
        ]
        : [
        ]
    ),
    // }}}
  ]) . "\n" ?>
<?php if ($rule->key === 'nawabari') { ?>
  <p class="text-right">
    <?= Html::encode(Yii::t('app', 'Inking Performance')) ?>:
    <a href="https://twitter.com/splatoon_weapon/status/958523893878149121" target="_blank">https://twitter.com/splatoon_weapon/status/958523893878149121</a>
  </p>
  <div>
    <?= Html::tag(
      'h5',
      Html::encode(Yii::t('app', 'Inking Performance vs Win %')),
      [
        'id' => sprintf('ink-performance-%s', $rule->key),
        'class' => 'text-center',
      ]
    ) . "\n" ?>
<?php $_list = array_map(
  function ($model) : array {
    return [
      (float)$model->ink_performance,
      (float)$model->wp,
      $model->name,
      (int)$model->count
    ];
  },
  $rule->data->weapons
);
usort($_list, function ($a, $b) {
  return $a[0] <=> $b[0];
});
$jsonId = sprintf('inkperformance-%s-data', $rule->key);
?>
    <?= Html::tag(
      'script',
      Json::encode($_list),
      [
        'type' => 'application/json',
        'id' => $jsonId,
      ]
    ) . "\n" ?>
    <?= Html::tag('div', '', [
      'class' => 'graph graph-inkperformance',
      'data' => [
        'source' => $jsonId,
        'label-correlation-coefficient' => Yii::t('app', 'Correlation Coefficient'),
      ]
    ]) . "\n" ?>
  </div>
<?php } ?>
  <?= GridView::widget([
    // {{{
    'options' => ['class' => 'table-responsive table-responsive-force'],
    'tableOptions' => ['class' => 'table table-striped table-condensed table-sortable'],
    'layout' => '{items}',
    'dataProvider' => new ArrayDataProvider([
      'allModels' => $rule->type,
      'pagination' => false,
      'sort' => false,
    ]),
    'columns' => [
      [
        'label' => Yii::t('app', 'Category'), // {{{
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
            'data-sort-value' => $model->avg_kill ?? null,
          ];
        },
        'value' => function ($model) : string {
          if (($model->avg_kill ?? null) === null) {
            return '';
          }
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
            'data-sort-value' => $model->avg_death ?? null,
          ];
        },
        'value' => function ($model) : string {
          if (($model->avg_death ?? null) === null) {
            return '';
          }
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
            'data-sort-value' => $model->kill_ratio ?? null,
          ];
        },
        'format' => 'raw',
        'value' => function ($model) : string {
          if (($model->kill_ratio ?? null) === null) {
            return '';
          }

          return implode(' ', [
            Html::encode(Yii::$app->formatter->asDecimal($model->kill_ratio, 3)),
            KillRatioBadgeWidget::widget([
              'killRatio' => $model->kill_ratio,
            ]),
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
            'data-sort-value' => $model->avg_special ?? null,
          ];
        },
        'value' => function ($model) : string {
          if (($model->avg_special ?? null) === null) {
            return '';
          }
          return Yii::$app->formatter->asDecimal($model->avg_special, 2);
        },
        // }}}
      ],
    ],
    // }}}
  ]) . "\n" ?>
  <?= GridView::widget([
    // {{{
    'options' => ['class' => 'table-responsive table-responsive-force'],
    'tableOptions' => ['class' => 'table table-striped table-condensed table-sortable'],
    'layout' => '{items}',
    'dataProvider' => new ArrayDataProvider([
      'allModels' => $rule->category,
      'pagination' => false,
      'sort' => false,
    ]),
    'columns' => [
      [
        'label' => Yii::t('app', 'Category'), // {{{
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
            'data-sort-value' => $model->avg_kill ?? null,
          ];
        },
        'value' => function ($model) : string {
          if (($model->avg_kill ?? null) === null) {
            return '';
          }
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
            'data-sort-value' => $model->avg_death ?? null,
          ];
        },
        'value' => function ($model) : string {
          if (($model->avg_death ?? null) === null) {
            return '';
          }
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
            'data-sort-value' => $model->kill_ratio ?? null,
          ];
        },
        'format' => 'raw',
        'value' => function ($model) : string {
          if (($model->kill_ratio ?? null) === null) {
            return '';
          }
          return implode(' ', [
            Html::encode(Yii::$app->formatter->asDecimal($model->kill_ratio, 3)),
            KillRatioBadgeWidget::widget([
              'killRatio' => $model->kill_ratio,
            ]),
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
            'data-sort-value' => $model->avg_special ?? null,
          ];
        },
        'value' => function ($model) : string {
          if (($model->avg_special ?? null) === null) {
            return '';
          }
          return Yii::$app->formatter->asDecimal($model->avg_special, 2);
        },
        // }}}
      ],
    ],
    // }}}
  ]) . "\n" ?>
  <?= GridView::widget([
    // {{{
    'options' => ['class' => 'table-responsive table-responsive-force'],
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
        'headerOptions' => [
          'data-sort' => 'string',
        ],
        'contentOptions' => function ($model): array {
          return [
            'data-sort-value' => $model->name,
          ];
        },
        'format' => 'raw',
        'value' => function (stdClass $model): string {
          $asset = Spl2WeaponAsset::register($this);
          return implode(' ', [
            Html::img($asset->getIconUrl('sp/' . $model->key), [
              'class' => 'auto-tooltip',
              'title' => $model->name,
              'style' => [
                'width' => 'auto',
                'height' => '1.5em',
              ],
            ]),
            Html::encode($model->name),
          ]);
        },
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
            'data-sort-value' => $model->avg_kill ?? null,
          ];
        },
        'value' => function ($model) : string {
          if (($model->avg_kill ?? null) === null) {
            return '';
          }
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
            'data-sort-value' => $model->avg_death ?? null,
          ];
        },
        'value' => function ($model) : string {
          if (($model->avg_death ?? null) === null) {
            return '';
          }
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
            'data-sort-value' => $model->kill_ratio ?? null,
          ];
        },
        'format' => 'raw',
        'value' => function ($model) : string {
          if (($model->kill_ratio ?? null) === null) {
            return '';
          }
          return implode(' ', [
            Html::encode(Yii::$app->formatter->asDecimal($model->kill_ratio, 3)),
            KillRatioBadgeWidget::widget([
              'killRatio' => $model->kill_ratio,
            ]),
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
            'data-sort-value' => $model->avg_special ?? null,
          ];
        },
        'value' => function ($model) : string {
          if (($model->avg_special ?? null) === null) {
            return '';
          }
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
  <?= GridView::widget([
    // {{{
    'options' => ['class' => 'table-responsive table-responsive-force'],
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
        'headerOptions' => [
          'data-sort' => 'string',
        ],
        'contentOptions' => function (stdClass $model): array {
          return [
            'data-sort-value' => $model->name,
          ];
        },
        'format' => 'raw',
        'value' => function (stdClass $model): string {
          $asset = Spl2WeaponAsset::register($this);
          return implode(' ', [
            Html::img($asset->getIconUrl('sub/' . $model->key), [
              'class' => 'auto-tooltip',
              'title' => $model->name,
              'style' => [
                'width' => 'auto',
                'height' => '1.5em',
              ],
            ]),
            Html::encode($model->name),
          ]);
        },
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
            'data-sort-value' => $model->avg_kill ?? null,
          ];
        },
        'value' => function ($model) : string {
          if (($model->avg_kill ?? null) === null) {
            return  '';
          }
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
            'data-sort-value' => $model->avg_death ?? null,
          ];
        },
        'value' => function ($model) : string {
          if (($model->avg_death ?? null) === null) {
            return  '';
          }
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
            'data-sort-value' => $model->kill_ratio ?? '',
          ];
        },
        'format' => 'raw',
        'value' => function ($model) : string {
          if (($model->kill_ratio ?? null) === null) {
            return '';
          }
          return implode(' ', [
            Html::encode(Yii::$app->formatter->asDecimal($model->kill_ratio, 3)),
            KillRatioBadgeWidget::widget([
              'killRatio' => $model->kill_ratio,
            ]),
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
            'data-sort-value' => $model->avg_special ?? null,
          ];
        },
        'value' => function ($model) : string {
          if (($model->avg_special ?? null) === null) {
            return '';
          }
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
<?php } ?>
</div>
