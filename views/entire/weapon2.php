<?php
use app\assets\AppOptAsset;
use app\assets\MapImage2Asset;
use app\assets\SortableTableAsset;
use app\components\widgets\AdWidget;
use app\components\widgets\SnsWidget;
use app\components\widgets\WinLoseLegend;
use app\models\RankGroup2;
use app\models\Rule2;
use app\models\SplatoonVersion2;
use app\models\StatWeapon2UseCountPerWeek;
use app\models\Weapon2;
use app\models\WeaponCategory2;
use jp3cki\yii2\flot\FlotAsset;
use jp3cki\yii2\flot\FlotPieAsset;
use jp3cki\yii2\flot\FlotTimeAsset;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;

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

$optAsset = AppOptAsset::register($this);

FlotAsset::register($this);
FlotPieAsset::register($this);
FlotTimeAsset::register($this);
SortableTableAsset::register($this);

$this->registerCss('.graph{height:300px}');
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
<?php $optAsset->registerJsFile($this, 'weapon2.js') ?>
    <?= Html::dropDownList(
      false,
      $weapon->key,
      (function () use ($query) : array {
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
                function (Weapon2 $weapon) : string {
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
        function (Rule2 $tmp) use ($rule, $weapon) : string {
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
        Html::tag('span', '', ['class' => 'fa fa-fw fa-exchange']),
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
          'rule5' => '@gachi'
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
$sum = function (string $column) use ($weapon) : string {
    return sprintf(
        'SUM(CASE %s END)',
        sprintf(
            'WHEN [[weapon_id]] = %d THEN [[%s]]',
            $weapon->id,
            $column
        ),
        'ELSE 0'
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
      function (array $row) use ($normalizedSeconds) : array {
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
    <?= Html::encode(Yii::t('app', 'These data were totaled after normalize to 5 minutes for each battle.')) . "\n" ?>
  </p>
  <p>
    <?= Html::encode(Yii::t('app', 'Earlier Turf-Inked data are currently wrong. It will be fixed in the near future.')); ?>
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
          function (array $group) : string {
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
            function (array $version) : string {
              return Yii::t('app-version2', $version['name']);
            }
          );
          uksort($list, function (string $a, string $b) : int {
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
<?php $_mapImage = MapImage2Asset::register($this) ?>
    <table class="table table-condensed table-sortable graph-container">
      <thead>
        <tr>
          <th data-sort="int"><?= Html::encode(Yii::t('app', 'Stage')) ?></th>
          <th data-sort="float"><?= Html::encode(Yii::t('app', 'Win %')) ?></th>
          <th data-sort="float"><?= Html::encode(Yii::t('app', 'Kills')) ?></th>
          <th data-sort="float"><?= Html::encode(Yii::t('app', 'Deaths')) ?></th>
          <th data-sort="float"><?= Html::encode(Yii::t('app', 'Specials')) ?></th>
          <th data-sort="float"><?= Html::encode(Yii::t('app', 'Assist')) ?></th>
        </tr>
      </thead>
      <tbody>
<?php $_mapCnt = 0 ?>
<?php foreach ($maps as $_mapKey => $_mapName) { ?>
<?php $_imgFileName = "daytime/{$_mapKey}.jpg"; ?>
        <tr>
          <?= Html::tag(
            'th',
            implode('', [
              Html::encode($_mapName) . '<br>',
              (file_exists(Yii::$app->assetManager->getAssetPath($_mapImage, $_imgFileName))
                ? Html::img(
                  Yii::$app->assetManager->getAssetUrl($_mapImage, $_imgFileName),
                  ['style' => [
                    'max-width' => '100%',
                  ]]
                )
                : ''
              ),
            ]),
            [
              'data' => [
                'sort-value' => $_mapCnt++,
              ]
            ]
          ) . "\n" ?>
          <?= Html::tag(
            'td',
            isset($winRate[$_mapKey])
              ? Html::tag(
                'div',
                '',
                [
                  'class' => 'pie-flot-container',
                  'data' => [
                    'json' => Json::encode($winRate[$_mapKey]),
                  ],
                ]
              )
              : '',
            [
              'data' => [
                'sort-value' => isset($winRate[$_mapKey])
                  ? (function (array $data) : string {
                    return sprintf(
                      '%f',
                      ($data['win'] + $data['lose'] > 0)
                        ? ($data['win'] * 100 / ($data['win'] + $data['lose']))
                        : -1.0
                    );
                  })($winRate[$_mapKey])
                  : '-1.0',
              ],
            ]
          ) . "\n" ?>
          <?= Html::tag(
            'td',
            isset($kills[$_mapKey])
              ? Html::tag(
                'div',
                '',
                [
                  'class' => 'bar-flot-container',
                  'data' => [
                    'type' => 'kill',
                    'json' => Json::encode(array_slice($kills[$_mapKey], 0)),
                  ],
                ]
              )
              : '',
            [
              'data' => [
                'sort-value' => isset($kills[$_mapKey])
                  ? (function (array $list) : string {
                    $battles = 0;
                    $kills = 0;
                    foreach ($list as $row) {
                      $battles += $row['battles'];
                      $kills += $row['battles'] * $row['times'];
                    }
                    return sprintf('%f', $battles > 0 ? $kills / $battles : -0.1);
                  })($kills[$_mapKey])
                  : '-1.0',
              ],
            ]
          ) . "\n" ?>
          <?= Html::tag(
            'td',
            isset($deaths[$_mapKey])
              ? Html::tag(
                'div',
                '',
                [
                  'class' => 'bar-flot-container',
                  'data' => [
                    'type' => 'death',
                    'json' => Json::encode(array_slice($deaths[$_mapKey], 0)),
                  ],
                ]
              )
              : '',
            [
              'data' => [
                'sort-value' => isset($deaths[$_mapKey])
                  ? (function (array $list) : string {
                    $battles = 0;
                    $deaths = 0;
                    foreach ($list as $row) {
                      $battles += $row['battles'];
                      $deaths += $row['battles'] * $row['times'];
                    }
                    return sprintf('%f', $battles > 0 ? $deaths / $battles : -0.1);
                  })($deaths[$_mapKey])
                  : '-1.0',
              ],
            ]
          ) . "\n" ?>
          <?= Html::tag(
            'td',
            isset($specials[$_mapKey])
              ? Html::tag(
                'div',
                '',
                [
                  'class' => 'bar-flot-container',
                  'data' => [
                    'type' => 'special',
                    'json' => Json::encode(array_slice($specials[$_mapKey], 0)),
                  ],
                ]
              )
              : '',
            [
              'data' => [
                'sort-value' => isset($specials[$_mapKey])
                  ? (function (array $list) : string {
                    $battles = 0;
                    $specials = 0;
                    foreach ($list as $row) {
                      $battles += $row['battles'];
                      $specials += $row['battles'] * $row['times'];
                    }
                    return sprintf('%f', $battles > 0 ? $specials / $battles : -0.1);
                  })($specials[$_mapKey])
                  : '-1.0',
              ],
            ]
          ) . "\n" ?>
          <?= Html::tag(
            'td',
            isset($assists[$_mapKey])
              ? Html::tag(
                'div',
                '',
                [
                  'class' => 'bar-flot-container',
                  'data' => [
                    'type' => 'assist',
                    'json' => Json::encode(array_slice($assists[$_mapKey], 0)),
                  ],
                ]
              )
              : '',
            [
              'data' => [
                'sort-value' => isset($assists[$_mapKey])
                  ? (function (array $list) : string {
                    $battles = 0;
                    $assists = 0;
                    foreach ($list as $row) {
                      $battles += $row['battles'];
                      $assists += $row['battles'] * $row['times'];
                    }
                    return sprintf('%f', $battles > 0 ? $assists / $battles : -0.1);
                  })($assists[$_mapKey])
                  : '-1.0',
              ],
            ]
          ) . "\n" ?>
        </tr>
<?php } ?>
      </tbody>
    </table>
  </div>
</div>
<?php
$this->registerCss(implode('', [
  '.pie-flot-container{height:200px}',
  '.pie-flot-container .error{display:none}',
  '.bar-flot-container{min-width:150px}',
  '.graph-container thead tr:nth-child(1) th{width:17%;min-width:100px}',
  '.graph-container thead tr:nth-child(1) th:nth-child(1){width:15%;min-width:80px}',
]));

// update pie charts
$this->registerJs('(function(){window.statByMapRule()})();');
?>
