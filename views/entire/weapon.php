<?php

/**
 * @copyright Copyright (C) 2020-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\assets\EntireWeaponAsset;
use app\assets\InlineListAsset;
use app\components\widgets\AdWidget;
use app\components\widgets\FA;
use app\components\widgets\SnsWidget;
use app\components\widgets\WinLoseLegend;
use app\models\Rule;
use app\models\SummarizedWeaponVsWeapon;
use yii\data\ArrayDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;

$this->context->layout = 'main';
$title = Yii::t('app', 'Weapon | {weapon} | {rule}', [
  'weapon' => Yii::t('app-weapon', $weapon->name),
  'rule' => Yii::t('app-rule', $rule->name),
]);
$this->title = implode(' | ', [
  Yii::$app->name,
  $title,
]);

$this->registerMetaTag(['name' => 'twitter:card', 'content' => 'summary']);
$this->registerMetaTag(['name' => 'twitter:title', 'content' => $title]);
$this->registerMetaTag(['name' => 'twitter:description', 'content' => $title]);
$this->registerMetaTag(['name' => 'twitter:site', 'content' => '@stat_ink']);

$this->registerJsVar('kddata', $killDeath);
$this->registerJsVar('mapdata', $mapWP);

EntireWeaponAsset::register($this);
?>
<div class="container">
  <h1><?= Html::encode(implode(' - ', [
    Yii::t('app-weapon', $weapon->name),
    Yii::t('app-rule', $rule->name),
  ])) ?></h1>

  <?= AdWidget::widget() . "\n" ?>
  <?= SnsWidget::widget() . "\n" ?>

  <p>
    <?= Html::dropDownList(
      'change-weapon',
      $weapon->key,
      $weapons,
      [
        'class' => 'form-control',
        'id' => 'change-weapon',
        'data' => [
          'url' => Url::to(['entire/weapon', 'weapon' => 'WEAPON_KEY', 'rule' => $rule->key], true),
        ],
      ]
    ) . "\n" ?>
  </p>
<?php $this->registerJs(<<<'EOF'
jQuery('#change-weapon').change(function(){
  var $select = $(this);
  window.location.href = $select.data('url').replace('WEAPON_KEY', function() {
    return encodeURIComponent($select.val());
  });
});
EOF
); ?>

  <nav>
<?php InlineListAsset::register($this) ?>
    <ul class="inline-list"><?= implode('', array_map(
      function (Rule $_rule) use ($rule, $weapon): string {
        return Html::tag('li', ($_rule->key === $rule->key)
          ? Html::encode($_rule->name)
          : Html::a(
            Html::encode($_rule->name),
            ['entire/weapon',
                'weapon' => $weapon->key,
                'rule' => $_rule->key,
            ],
          )
        );
      },
      $rules
    )) ?></ul>
  </nav>
  
  <script id="use-pct-json" type="application/json"><?= Json::encode($useCount) ?></script>
  <?= Html::tag(
    'h2',
    Html::encode(Yii::t('app-rule', $rule->name)),
    ['id' => $rule->key]
  ) . "\n" ?>
  <h3><?= Html::encode(Yii::t('app', 'Use %')) ?></h3>
  <p>
<?php $_form = [
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
] ?>
    <?= Html::a(
      implode(' ', [
        (string)FA::fas('exchange-alt')->fw(),
        Html::encode(Yii::t('app', 'Compare number of uses')),
      ]),
      ['entire/weapons-use', 'cmp' => $_form],
      ['class' => 'btn btn-default']
    ) . "\n" ?>
  </p>
  <div class="graph stat-use-pct">
  </div>

  <h3><?= Html::encode(Yii::t('app', 'Kills and Deaths')) ?></h3>
  <p><?= implode('<br>', [
    vsprintf('%s %s', [
      Html::encode(Yii::t('app', 'Kills (average):')),
      Html::tag('span', '', ['class' => 'kd-summary', 'data-type' => 'kill-avg']),
    ]),
    vsprintf('%s %s', [
      Html::encode(Yii::t('app', 'Deaths (average):')),
      Html::tag('span', '', ['class' => 'kd-summary', 'data-type' => 'death-avg']),
    ]),
  ]) ?></p>
  <?= Html::tag('div', '', [
    'class' => 'graph stat-kill-death',
    'data' => [
      'legends-kill' => vsprintf('%s (%s)', [
        Yii::t('app', 'Battles'),
        Yii::t('app', 'Kills'),
      ]),
      'legends-death' => vsprintf('%s (%s)', [
        Yii::t('app', 'Battles'),
        Yii::t('app', 'Deaths'),
      ]),
    ],
  ]) . "\n" ?>

  <h3><?= Html::encode(Yii::t('app', 'Based on kills')) ?></h3>
  <div class="row">
    <div class="col-xs-12 col-sm-12 col-md-6 col-xl-6">
      <?= Html::tag('div', '', [
        'class' => 'graph stat-wp',
        'data-base' => 'kill',
        'data-scale' => 'no',
        'data-legends-win' => vsprintf('%s (%s)', [
          Yii::t('app', 'Battles'),
          Yii::t('app', 'Win'),
        ]),
        'data-legends-lose' => vsprintf('%s (%s)', [
          Yii::t('app', 'Battles'),
          Yii::t('app', 'Lose'),
        ]),
      ]) . "\n" ?>
    </div>
    <div class="col-xs-12 col-sm-12 col-md-6 col-xl-6">
      <?= Html::tag('div', '', [
        'class' => 'graph stat-wp',
        'data-base' => 'kill',
        'data-scale' => 'yes',
        'data-legends-win' => vsprintf('%s (%s)', [
          Yii::t('app', 'Win %'),
          Yii::t('app', 'Win'),
        ]),
        'data-legends-lose' => vsprintf('%s (%s)', [
          Yii::t('app', 'Win %'),
          Yii::t('app', 'Lose'),
        ]),
      ]) . "\n" ?>
    </div>
  </div>
  <h3><?= Html::encode(Yii::t('app', 'Based on deaths')) ?></h3>
  <div class="row">
    <div class="col-xs-12 col-sm-12 col-md-6 col-xl-6">
      <?= Html::tag('div', '', [
        'class' => 'graph stat-wp',
        'data-base' => 'death',
        'data-scale' => 'no',
        'data-legends-win' => vsprintf('%s (%s)', [
          Yii::t('app', 'Battles'),
          Yii::t('app', 'Win'),
        ]),
        'data-legends-lose' => vsprintf('%s (%s)', [
          Yii::t('app', 'Battles'),
          Yii::t('app', 'Lose'),
        ]),
      ]) . "\n" ?>
    </div>
    <div class="col-xs-12 col-sm-12 col-md-6 col-xl-6">
      <?= Html::tag('div', '', [
        'class' => 'graph stat-wp',
        'data-base' => 'death',
        'data-scale' => 'yes',
        'data-legends-win' => vsprintf('%s (%s)', [
          Yii::t('app', 'Win %'),
          Yii::t('app', 'Win'),
        ]),
        'data-legends-lose' => vsprintf('%s (%s)', [
          Yii::t('app', 'Win %'),
          Yii::t('app', 'Lose'),
        ]),
      ]) . "\n" ?>
    </div>
  </div>

  <h3><?= Html::encode(Yii::t('app', 'Winning Percentage based on K/D')) ?></h3>
  <p>
    <?= Html::a(
      Html::encode(Yii::t('app', 'Winning Percentage based on K/D')),
      ['entire/kd-win',
        'filter' => ['weapon' => $weapon->key],
        '#' => $rule->key
      ],
    ) . "\n" ?>
  </p>
  <h3>
    <?= Html::encode(Yii::t('app', 'Stage')) . "\n" ?>
  </h3>
  <?= WinLoseLegend::widget() . "\n" ?>
  <div class="row"><?= implode('', array_map(
    function (array $map) use ($mapWP): string {
      return Html::tag(
        'div',
        implode('', [
          Html::tag('h4', Html::encode($map['name'])),
          Html::tag('div', '', [
            'class' => 'graph stat-map-wp',
            'data-data' => Json::encode($mapWP[$map['key']] ?? []),
          ]),
        ]),
        ['class' => 'col-12 col-xs-12 col-sm-4 col-md-3']
      );
    },
    $maps
  )) ?></div>

  <h3 id="vs-weapon">ブキ別対戦成績</h3>
  <div class="table-responsive">
    <?= GridView::widget([
      'dataProvider' => Yii::createObject([
        '__class' => ArrayDataProvider::class,
        'allModels' => SummarizedWeaponVsWeapon::find($weapon->id, $rule->id),
        'sort' => false,
        'pagination' => false,
      ]),
      'tableOptions' => [
        'class' => 'table table-striped table-condensed',
      ],
      'layout' => '{items}',
      'emptyText' => Yii::t('app', 'There are no data.'),
      'columns' => [
        [
          'label' => Yii::t('app', 'Weapon'),
          'format' => 'raw',
          'value' => function ($model): string {
            if (!$weapon = $model->rhsWeapon) {
              return Html::encode('?');
            }

            $subWeapon = $weapon->subweapon;
            $spWeapon = $weapon->special;
            if (!$subWeapon || !$spWeapon) {
              return Html::encode(Yii::t('app-weapon', $weapon->name));
            }

            return Html::tag('span', Html::encode(Yii::t('app-weapon', $weapon->name)), [
              'class' => 'auto-tooltip',
              'title' => implode(' / ', [
                sprintf('%s %s', Yii::t('app', 'Sub:'), Yii::t('app-subweapon', $subWeapon->name)),
                sprintf('%s %s', Yii::t('app', 'Special:'), Yii::t('app-special', $spWeapon->name)),
              ]),
            ]);
          },
        ],
        [
          'label' => Yii::t('app', 'Battles'),
          'contentOptions' => [
            'class' => 'text-right',
          ],
          'format' => 'integer',
          'value' => function ($model): int {
            return (int)$model->battle_count;
          },
        ],
        [
          'label' => Yii::t('app', 'Win %'),
          'format' => 'raw',
          'headerOptions' => [
            'style' => [
              'min-width' => '200px',
            ],
          ],
          'value' => function ($model): string {
            $value = $model->winPct;
            if (is_nan($value)) {
              return '';
            }

            return Html::tag(
              'div',
              Html::tag(
                'div',
                Html::encode(Yii::$app->formatter->asPercent($value / 100, 2)),
                [
                  'class' => 'progress-bar',
                  'style' => [
                    'width' => $value . '%',
                  ],
                ]
              ),
              [
                'class' => 'progress',
              ]
            );
          },
        ],
      ],
    ]) . "\n" ?>
  </div>
</div>
<?php $this->registerJs('jQuery(window).resize();'); ?>
