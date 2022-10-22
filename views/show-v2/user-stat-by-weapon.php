<?php

declare(strict_types=1);

use app\assets\Spl2WeaponAsset;
use app\components\widgets\AdWidget;
use app\components\widgets\Battle2FilterWidget;
use app\components\widgets\BattleSummaryItemWidget;
use app\components\widgets\SnsWidget;
use app\components\widgets\UserMiniInfo2;
use statink\yii2\sortableTable\SortableTableAsset;
use yii\data\ArrayDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var View $this
 */

$title = Yii::t('app', "{name}'s Battle Stats (by Weapon)", ['name' => $user->name]);
$this->title = Yii::$app->name . ' | ' . $title;

$this->registerMetaTag(['name' => 'twitter:card', 'content' => 'summary']);
$this->registerMetaTag(['name' => 'twitter:title', 'content' => $title]);
$this->registerMetaTag(['name' => 'twitter:description', 'content' => $title]);
$this->registerMetaTag(['name' => 'twitter:site', 'content' => '@stat_ink']);
$this->registerMetaTag(['name' => 'twitter:image', 'content' => $user->iconUrl]);
if ($user->twitter != '') {
  $this->registerMetaTag(['name' => 'twitter:creator', 'content' => '@' . $user->twitter]);
}

SortableTableAsset::register($this);

?>
<div class="container">
  <h1>
    <?= Html::encode($title) . "\n" ?>
  </h1>

  <?= SnsWidget::widget() . "\n" ?>

  <div class="row">
    <div class="col-xs-12 col-sm-8 col-lg-9">
<?php
$dataColumn = function (string $label, string $colKey, ?string $longLabel = null) : array {
  // {{{
  if ($longLabel === null) {
    $longLabel = $label;
  }
  return [
    'label' => Yii::t('app', $label),
    'headerOptions' => [
      'data-sort' => 'float',
    ],
    'contentOptions' => function (array $row) use ($colKey) : array {
      return [
        'class' => 'text-right',
        'data' => [
          'sort-value' => $row['avg_' . $colKey] ?? '-1',
        ],
      ];
    },
    'format' => 'raw',
    'value' => function (array $row) use ($colKey, $longLabel) : string {
      if ($row['avg_' . $colKey] === null) {
        return '';
      }

      $f = function (?float $value, int $dec) : string {
        return $value === null
          ? '?'
          : Yii::$app->formatter->asDecimal($value, $dec);
      };

      return BattleSummaryItemWidget::widget([
        'battles'   => $row['battles'],
        'total'     => (int)round($row['battles'] * $row["avg_{$colKey}"]),
        'min'       => $row["min_{$colKey}"],
        'max'       => $row["max_{$colKey}"],
        'q1'        => $row["q1_{$colKey}"],
        'q3'        => $row["q3_{$colKey}"],
        'median'    => $row["med_{$colKey}"],
        'pct5'      => $row["p5_{$colKey}"],
        'pct95'     => $row["p95_{$colKey}"],
        'stddev'    => $row["sd_{$colKey}"],
        'tooltipText' => null,
        'summary'   => vsprintf('%s - %s', [
          $row['weapon_name'],
          Yii::t('app', $longLabel),
        ]),
      ]);
    },
  ];
  // }}}
};
?>
      <?= GridView::widget([
        'dataProvider' => new ArrayDataProvider([
          'allModels' => $list,
          'sort' => false,
          'pagination' => false,
        ]),
        'layout' => '{items}',
        'emptyText' => Yii::t('app', 'There are no data.'),
        'tableOptions' => [
          'class' => [
            'table',
            'table-striped',
            'table-sortable',
          ],
        ],
        'columns' => [
          [
            'label' => Yii::t('app', 'Weapon'), // {{{
            'headerOptions' => [
              'data-sort' => 'string',
            ],
            'contentOptions' => function (array $model): array {
              return [
                'data-sort-value' => $model['weapon_name'],
              ];
            },
            'format' => 'raw',
            'value' => function (array $model): string {
              $asset = Spl2WeaponAsset::register($this);
              return implode(' ', [
                Html::img($asset->getIconUrl($model['weapon_key']), [
                  'style' => [
                    'height' => '1.5em',
                  ],
                ]),
                Html::encode($model['weapon_name']),
              ]);
            },
            // }}}
          ],
          [
            // Sub Weapon {{{
            'label' => Html::tag(
              'span',
              Html::encode(Yii::t('app', 'Sub Weapon')),
              ['class' => 'sr-only']
            ),
            'encodeLabel' => false,
            'format' => 'raw',
            'value' => function (array $model): string {
              $asset = Spl2WeaponAsset::register($this);
              return Html::img($asset->getIconUrl('sub/' . $model['subweapon_key']), [
                'style' => [
                  'height' => '1.333em',
                ],
                'title' => $model['subweapon_name'],
                'class' => 'auto-tooltip',
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
            'value' => function (array $model): string {
              $asset = Spl2WeaponAsset::register($this);
              return Html::img($asset->getIconUrl('sp/' . $model['special_key']), [
                'style' => [
                  'height' => '1.333em',
                ],
                'title' => $model['special_name'],
                'class' => 'auto-tooltip',
              ]);
            },
            // }}}
          ],
          [
            // Battles {{{
            'header' => implode(' ', [
              Html::encode(Yii::t('app', 'Battles')),
              Html::tag('span', '', ['class' => 'arrow fa fa-angle-down']),
            ]),
            'headerOptions' => [ 'data-sort' => 'int' ],
            'format' => 'raw',
            'contentOptions' => function (array $row) : array {
              return [
                'class' => 'text-right',
                'data-sort-value' => (string)(int)$row['battles'],
              ];
            },
            'value' => function (array $row) use ($user, $filter) : string {
              return Html::a(Html::encode(
                Yii::$app->formatter->asInteger($row['battles'])
              ), [
                'show-v2/user',
                'screen_name' => $user->screen_name,
                'filter' => array_merge($filter->toQueryParams(''), ['weapon' => $row['weapon_key']]),
              ]);
            }
            // }}}
          ],
          [
            // Win % {{{
            'label' => Yii::t('app', 'Win %'),
            'headerOptions' => [
              'data-sort' => 'float',
              'style' => ['min-width' => '150px'],
            ],
            'contentOptions' => function (array $row) : array {
              return [
                'data-sort-value' => $row['win_rate'],
              ];
            },
            'format' => 'raw',
            'value' => function (array $row) : string {
              return Html::tag(
                'div',
                Html::tag(
                  'div',
                  Html::encode(Yii::$app->formatter->asPercent($row['win_rate'], 1)),
                  [
                    'class' => 'progress-bar',
                    'role' => 'progress',
                    'aria-valuenow' => $row['win_rate'],
                    'aria-valuemin' => '0',
                    'aria-valuemax' => '1',
                    'style' => [
                      'width' => ($row['win_rate'] * 100) . '%',
                    ],
                  ]
                ),
                [
                  'class' => 'progress',
                  'style' => ['margin-bottom' => '0'],
                ]
              );
            },
            // }}}
          ],
          $dataColumn('k', 'kill', 'Kills'),
          $dataColumn('d', 'death', 'Deaths'),
          $dataColumn('k+a', 'ka', 'Kill or Assist'),
          $dataColumn('sp', 'sp', 'Specials'),
          [
            // Kill Ratio {{{
            'label' => Yii::t('app', 'Ratio'),
            'headerOptions' => [ 'data-sort' => 'float' ],
            'contentOptions' => function (array $row) : array {
              $value = null;
              if ($row['avg_kill'] !== null && $row['avg_death'] !== null) {
                if ($row['avg_death'] == 0.0) {
                  $value = ($row['avg_kill'] == 0.0 ? null : 100);
                } else {
                  $value = $row['avg_kill'] / $row['avg_death'];
                }
              }
              return [
                'class' => 'text-right',
                'data-sort-value' => ($value === null ? -1 : $value),
              ];
            },
            'value' => function (array $row) : string {
              if ($row['avg_kill'] === null || $row['avg_death'] === null) {
                return '';
              }
              if ($row['avg_death'] == 0.0) {
                return ($row['avg_kill'] == 0.0)
                  ? Yii::t('app', 'N/A')
                  : Yii::$app->formatter->asDecimal(99.99, 2);
              }
              return Yii::$app->formatter->asDecimal($row['avg_kill'] / $row['avg_death'], 2);
            },
            // }}}
          ],
        ],
      ]) . "\n" ?>
    </div>
    <div class="col-xs-12 col-sm-4 col-lg-3">
      <?= Battle2FilterWidget::widget([
        'route' => 'show-v2/user-stat-by-weapon',
        'screen_name' => $user->screen_name,
        'filter' => $filter,
        'action' => 'summarize',
        'weapon' => false,
        'result' => false,
      ]) . "\n" ?>
      <?= UserMiniInfo2::widget(['user' => $user]) . "\n" ?>
      <?= AdWidget::widget() . "\n" ?>
    </div>
  </div>
</div>
