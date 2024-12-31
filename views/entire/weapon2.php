<?php

/**
 * @copyright Copyright (C) 2017-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\assets\EntireWeapon2Asset;
use app\components\widgets\AdWidget;
use app\components\widgets\FA;
use app\components\widgets\SnsWidget;
use app\components\widgets\WinLoseLegend;
use app\models\RankGroup2;
use app\models\Rule2;
use app\models\SplatoonVersion2;
use app\models\StatWeapon2UseCountPerWeek;
use app\models\Weapon2;
use app\models\WeaponCategory2;
use statink\yii2\stages\spl2\Spl2Stage;
use yii\bootstrap\ActiveForm;
use yii\data\ArrayDataProvider;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\View;

/**
 * @var Rule2 $rule
 * @var View $this
 * @var Weapon2 $weapon
 */

function calcError(int $battles, int $wins): ?float
{
  if ($battles < 1 || $wins < 0) {
    return null;
  }

  // ref. http://lfics81.techblog.jp/archives/2982884.html
  $winRate = $wins / $battles;
  $s = sqrt(($battles / ($battles - 1.5)) * $winRate * (1.0 - $winRate));
  return $s / sqrt($battles) * 100.0;
}

EntireWeapon2Asset::register($this);

$weaponName = Yii::t('app-weapon2', $weapon->name);
$ruleName = Yii::t('app-rule2', $rule->name);

$title = implode(' | ', [
  Yii::$app->name,
  Yii::t('app', 'Weapon'),
  $weaponName,
  $ruleName,
]);
$this->title = $title;

$this->registerMetaTag(['name' => 'twitter:card', 'content' => 'summary']);
$this->registerMetaTag(['name' => 'twitter:title', 'content' => $title]);
$this->registerMetaTag(['name' => 'twitter:description', 'content' => $title]);
$this->registerMetaTag(['name' => 'twitter:site', 'content' => '@stat_ink']);

$this->registerCss(implode('', [
  '.graph{height:300px}',
  '.pie-flot-container{height:200px}',
  '.pie-flot-container .error{display:none}',
  '.bar-flot-container{min-width:150px}',
  '.graph-container thead tr:nth-child(1) th{width:17%;min-width:100px}',
  '.graph-container thead tr:nth-child(1) th:nth-child(1){width:15%;min-width:80px}',
]));
?>
<div class="container">
  <h1>
    <?= implode(' - ', [
      Html::encode($weaponName),
      Html::encode($ruleName),
    ]) . "\n" ?>
  </h1>

  <?= AdWidget::widget() . "\n" ?>
  <?= SnsWidget::widget() . "\n" ?>

  <p class="form-inline">
<?php $query = WeaponCategory2::find()
  ->with([
    'weaponTypes' => function ($q) {
      $q->orderBy(['id' => SORT_ASC]);
    },
    'weaponTypes.weapons'
  ])
  ->orderBy(['id' => SORT_ASC]);
?>
    <?= Html::dropDownList(
      false,
      $weapon->key,
      (function () use ($query): array {
        // {{{
        $ret = [];
        foreach ($query->all() as $category) {
          foreach ($category->weaponTypes as $type) {
            $name = ($category->name === $type->name)
              ? Yii::t('app-weapon2', $category->name)
              : implode(' > ', [
                Yii::t('app-weapon2', $category->name),
                Yii::t('app-weapon2', $type->name),
              ]);
            $ret[$name] = (function (array $weapons) {
              $weapons = ArrayHelper::map(
                $weapons,
                'key',
                function (Weapon2 $weapon): string {
                  return Yii::t('app-weapon2', $weapon->name);
                }
              );
              uasort($weapons, 'strnatcasecmp');
              return $weapons;
            })($type->weapons);
          }
        }
        return $ret;
        // }}}
      })(),
      [
        'id' => 'change-weapon',
        'class' => 'form-control',
        'data' => [
          'url' => Url::to(['weapon2', 'rule' => $rule->key, 'weapon' => 'WEAPON_KEY'], true),
        ],
      ]
    ). "\n" ?>
  </p>

  <p>
    <?= implode(
      ' | ',
      array_map(
        function (Rule2 $tmp) use ($rule, $weapon): string {
          return ($tmp->key === $rule->key)
            ? Html::encode(Yii::t('app-rule2', $tmp->name))
            : Html::a(
              Html::encode(Yii::t('app-rule2', $tmp->name)),
              ['weapon2', 'weapon' => $weapon->key, 'rule' => $tmp->key]
            );
        },
        Rule2::find()->orderBy(['id' => SORT_ASC])->all()
      )
    ) . "\n" ?>
  </p>
  
  <?= Html::tag(
    'h2',
    Html::encode(Yii::t('app-rule2', $rule->name)),
    ['id' => $rule->key]
  ) . "\n" ?>
  <h3>
    <?= Html::encode(Yii::t('app', 'Use % and Win %')) . "\n" ?>
  </h3>
  <p>
    <?= Html::a(
      implode('', [
        FA::fas('exchange-alt')->fw(),
        Html::encode(Yii::t('app', 'Compare number of uses')),
      ]),
      ['weapons2-use',
        'cmp' => [
          'weapon1' => $weapon->key,
          'rule1' => 'nawabari',
          'weapon2' => $weapon->key,
          'rule2' => 'area',
          'weapon3' => $weapon->key,
          'rule3' => 'yagura',
          'weapon4' => $weapon->key,
          'rule4' => 'hoko',
          'weapon5' => $weapon->key,
          'rule5' => 'asari',
          'weapon6' => $weapon->key,
          'rule6' => '@gachi',
        ],
      ],
      ['class' => 'btn btn-default', 'disabled' => true]
    ) . "\n" ?>
  </p>
  <?= Html::tag(
    'div',
    '',
    [
      'class' => 'graph stat-use-pct',
      'data' => [
        'label-use-pct' => Yii::t('app', 'Use %'),
        'label-win-pct' => Yii::t('app', 'Win %'),
      ],
    ]
  ) . "\n"
  ?>
<?php
$sum = function (string $column) use ($weapon): string {
    return sprintf(
        'SUM(CASE %s ELSE 0 END)',
        sprintf(
            'WHEN [[weapon_id]] = %d THEN [[%s]]',
            $weapon->id,
            $column
        )
    );
};
$q = StatWeapon2UseCountPerWeek::find()
    ->andWhere(['and',
        ['rule_id' => $rule->id],
        ['or',
            ['>', 'isoyear', 2017],
            ['and',
                ['=', 'isoyear', 2017],
                ['>', 'isoweek', 31],
            ],
        ],
    ])
    ->select([
        'isoyear',
        'isoweek',
        'total_battles' => 'SUM([[battles]])',
        'weapon_battles' => $sum('battles'),
        'weapon_wins' => $sum('wins'),
        'kills' => $sum('kills_with_time'),
        'deaths' => $sum('deaths_with_time'),
        'kd_time' => $sum('kd_time_seconds'),
        'specials' => $sum('specials_with_time'),
        'sp_time' => $sum('specials_time_seconds'),
        'inked' => $sum('inked_with_time'),
        'inked_time' => $sum('inked_time_seconds'),
    ])
    ->groupBy('[[isoyear]], [[isoweek]]')
    ->orderBy('[[isoyear]], [[isoweek]]');
$normalizedSeconds = ($rule->key == 'nawabari' ? 3 : 5) * 60;
?>
  <?= Html::tag(
    'script',
    Json::encode(array_map(
      function (array $row) use ($normalizedSeconds): array {
        return [
          'date' => (new DateTimeImmutable())
            ->setTimeZone(new DateTimeZone(Yii::$app->timeZone))
            ->setISODate($row['isoyear'], $row['isoweek'])
            ->format('Y-m-d'),
          'use_pct' => ($row['total_battles'] > 0)
            ? $row['weapon_battles'] * 100 / $row['total_battles']
            : null,
          'win_pct' => ($row['weapon_battles'] > 0)
            ? $row['weapon_wins'] * 100 / $row['weapon_battles']
            : null,
          'win_pct_err' => ($row['weapon_battles'] > 0)
            ? calcError((int)$row['weapon_battles'], (int)$row['weapon_wins'])
            : null,
          'kills' => ($row['kd_time'] > 0)
            ? $row['kills'] * $normalizedSeconds / $row['kd_time']
            : null,
          'deaths' => ($row['kd_time'] > 0)
            ? $row['deaths'] * $normalizedSeconds / $row['kd_time']
            : null,
          'specials' => ($row['sp_time'] > 0)
            ? $row['specials'] * $normalizedSeconds / $row['sp_time']
            : null,
          'inked' => ($row['inked_time'] > 0)
            ? $row['inked'] * $normalizedSeconds / $row['inked_time']
            : null,
        ];
      },
      $q->createCommand()->queryAll()
    )),
    ['id' => 'weekly-json', 'type' => 'application/json']
  ) . "\n" ?>
  <h3>
    <?= Html::encode(Yii::t('app', 'Stats')) . "\n" ?>
  </h3>
  <?= Html::tag(
    'div',
    '',
    [
      'class' => 'graph stat-kd-sp-inked',
      'data' => [
        'label-inked' => Yii::t('app', 'Avg Inked'),
        'label-kills' => Yii::t('app', 'Avg Kills'),
        'label-deaths' => Yii::t('app', 'Avg Deaths'),
        'label-specials' => Yii::t('app', 'Avg Specials'),
      ],
    ]
  ) . "\n"
  ?>
<?php if ($rule->key !== 'nawabari') { ?>
  <p>
    <?= Html::encode(Yii::t('app', 'This data was totaled after normalization to 5 minute intervals for each battle.')) . "\n" ?>
  </p>
  <p>
    <?= Html::encode(Yii::t('app', 'Earlier Turf-Inked data is currently wrong. It will be fixed in the near future.')); ?>
    <?= Html::a(
      Html::encode(Yii::t('app', 'Details')),
      'https://github.com/hymm/squid-tracks/issues/48'
    ) . "\n" ?>
  </p>
<?php } ?>

  <h2 id="stages">
    <?= Html::encode(Yii::t('app', 'Stages')) . "\n" ?>
  </h2>
  <?php $_form = ActiveForm::begin([
    'id' => 'stage-filter-form',
    'action' => ['entire/weapon2',
      'weapon' => $weapon->key,
      'rule' => $rule->key,
      '#' => 'stages',
    ],
    'method' => 'GET',
    'enableClientValidation' => false,
    'options' => [
      'class' => 'form-inline',
    ],
  ]); echo "\n" ?>
<?php if ($rule->key !== 'nawabari') { ?>
    <?= $_form->field($stageFilter, 'rank')
      ->label(false)
      ->dropDownList(array_merge(
        ['' => Yii::t('app-rank2', 'Any Rank')],
        ArrayHelper::map(
          RankGroup2::find()->orderBy(['rank' => SORT_DESC])->asArray()->all(),
          'key',
          function (array $group): string {
            return Yii::t('app-rank2', $group['name']);
          }
        )
      )) . "\n"
    ?>
<?php } ?>
    <?= $_form->field($stageFilter, 'version')
      ->label(false)
      ->dropDownList(array_merge(
        ['' => Yii::t('app-version2', 'Any Version')],
        (function () {
          $list = ArrayHelper::map(
            SplatoonVersion2::find()->asArray()->all(),
            'tag',
            function (array $version): string {
              return Yii::t('app-version2', $version['name']);
            }
          );
          uksort($list, function (string $a, string $b): int {
            return version_compare($b, $a);
          });
          return $list;
        })()
      )) . "\n"
    ?>
  <?php ActiveForm::end(); echo "\n" ?>
<?php $this->registerJs('(function($){"use strict";$(function(){$("#stage-filter-form select").change(function(){$("#stage-filter-form").submit()})})})(jQuery);'); ?>

  <?= WinLoseLegend::widget() . "\n" ?>

  <div class="table-responsive table-responsive-force">
<?php $_getQ = function (array $list, int $nth): ?int {
  while (count($list) > 0 && $nth > 0) {
    $row = array_shift($list);
    if ($nth <= $row['battles']) {
      return (int)$row['times'];
    }
    $nth -= $row['battles'];
  }
  return null;
} ?>
<?php $_dataColumn = function (string $label, string $type, array $data) use ($_getQ): array {
  return [
    'label' => Html::encode($label),
    'format' => 'raw',
    'value' => function (array $map) use ($type, $data, $_getQ): string {
      if (!$list = $data[$map['key']] ?? null) {
        return '';
      }

      $battles = array_sum(array_map(
        function (array $row): int {
          return (int)$row['battles'];
        },
        $list
      ));
      $additional = [];
      if ($battles > 0) {
        $total = array_sum(array_map(
          function (array $row): int {
            return (int)$row['battles'] * (int)$row['times'];
          },
          $list
        ));
        $average = $total / $battles;
        $stddev = sqrt(
          array_sum(
            array_map(
              function ($row) use ($average): float {
                return pow($row['times'] - $average, 2) * (int)$row['battles'];
              },
              $list
            )
          ) / $battles
        );

        $additional[Yii::t('app', 'Average')] = $average;
        $additional[Yii::t('app', 'Minimum')] = min(array_map(
          function (array $row): int {
            return (int)$row['times'];
          },
          $list
        ));
        if ($battles > 4) {
          $additional['Q 1/4'] = $_getQ($list, (int)round($battles / 4));
          $additional[Yii::t('app', 'Median')] = $_getQ($list, (int)round($battles / 2));
          $additional['Q 3/4'] = $_getQ($list, (int)round(3 * $battles / 4));
        }
        $additional[Yii::t('app', 'Maximum')] = max(array_map(
          function (array $row): int {
            return (int)$row['times'];
          },
          $list
        ));
        $additional['σ'] = $stddev;
      }

      return implode('', [
        Html::tag('div', '', [
          'class' => 'bar-flot-container',
          'data' => [
            'type' => $type,
            'json' => Json::encode(array_slice($data[$map['key']], 0)),
          ],
        ]),
        Html::tag('div', implode('<br>', array_filter(array_map(
          function (string $key, $value): ?string {
            if ($value === null || $value === false || is_nan($value)) {
              return null;
            }
            if (is_int($value)) {
              $value = Yii::$app->formatter->asInteger($value);
            } elseif (is_float($value)) {
              $value = Yii::$app->formatter->asDecimal($value, 2);
            }
            return Html::encode(sprintf('%s: %s', $key, $value));
          },
          array_keys($additional),
          array_values($additional)
        )))),
      ]);
    },
    'headerOptions' => ['data' => ['sort' => 'float']],
    'contentOptions' => function (array $map) use ($data): array {
      if (!$list = $data[$map['key']] ?? null) {
        return ['data' => ['sort-value' => '-1.0']];
      }
      $battles = array_sum(array_map(
        function (array $row): int {
          return (int)$row['battles'];
        },
        $list
      ));
      $value = array_sum(array_map(
        function (array $row): int {
          return (int)$row['battles'] * (int)$row['times'];
        },
        $list
      ));
      return ['data' => [
        'sort-value' => sprintf('%f', $battles > 0 ? ($value / $battles) : -0.1),
      ]];
    },
  ];
} ?>
    <?= GridView::widget([
      'dataProvider' => new ArrayDataProvider([
        'allModels' => array_map(
          function (string $key, string $name): array {
            return [
              'key' => $key,
              'name' => $name,
            ];
          },
          array_keys($maps),
          array_values($maps)
        ),
        'sort' => false,
        'pagination' => false,
      ]),
      'tableOptions' => [
        'class' => 'table table-striped table-condensed table-sortable graph-container',
      ],
      'summary' => false,
      'columns' => [
        [
          'label' => Html::encode(Yii::t('app', 'Stage')), // {{{
          'format' => 'raw',
          'value' => function (array $map): string {
            $imgFileName = sprintf('daytime/%s.jpg', $map['key']);
            return implode('<br>', [
              Html::tag('strong', Html::encode($map['name'])),
              Spl2Stage::img('daytime', $map['key'], ['style' => [
                'max-width' => '100%',
              ]]),
            ]);
          },
          'headerOptions' => ['data' => ['sort' => 'int']],
          'contentOptions' => function ($model, $key, $index, $column): array {
            return ['data' => [
              'sort-value' => $index,
            ]];
          },
          // }}}
        ],
        [
          'label' => Html::encode(Yii::t('app', 'Win %')), // {{{
          'format' => 'raw',
          'value' => function (array $map) use ($winRate): string {
            if (!isset($winRate[$map['key']])) {
              return '';
            }
            return Html::tag('div', '', [
              'class' => 'pie-flot-container',
              'data' => [
                'json' => Json::encode($winRate[$map['key']]),
              ],
            ]);
          },
          'headerOptions' => ['data' => ['sort' => 'float']],
          'contentOptions' => function (array $map) use ($winRate): array {
            $data = $winRate[$map['key']] ?? null;
            if (!$data || $data['win'] + $data['lose'] < 1) {
              return ['data' => ['sort-value' => '-1.0']];
            }
            return ['data' => [
              'sort-value' => sprintf('%f', ($data['win'] * 100.0 / ($data['win'] + $data['lose']))),
            ]];
          },
          // }}}
        ],
        $_dataColumn(Yii::t('app', 'Kills'), 'kill', $kills),
        $_dataColumn(Yii::t('app', 'Deaths'), 'death', $deaths),
        $_dataColumn(Yii::t('app', 'Specials'), 'special', $specials),
        $_dataColumn(Yii::t('app', 'Assist'), 'assist', $assists),
      ],
    ]) . "\n" ?>
  </div>
</div>
