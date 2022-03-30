<?php

declare(strict_types=1);

use app\assets\EntireKnockoutAsset;
use app\components\helpers\Html;
use app\components\widgets\AdWidget;
use app\components\widgets\SnsWidget;
use app\models\Knockout2FilterForm;
use app\models\Map2;
use app\models\RankGroup2;
use app\models\Rule2;
use statink\yii2\stages\spl2\Spl2Stage;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\web\View;
use yii\widgets\ActiveForm;

/**
 * @var Knockout2FilterForm $form
 * @var View $this
 * @var array $data
 */

$title = Yii::t('app', 'Knockout Ratio');
$this->title = Yii::$app->name . ' | ' . $title;

$this->registerMetaTag(['name' => 'twitter:card', 'content' => 'summary']);
$this->registerMetaTag(['name' => 'twitter:title', 'content' => $title]);
$this->registerMetaTag(['name' => 'twitter:description', 'content' => $title]);
$this->registerMetaTag(['name' => 'twitter:site', 'content' => '@stat_ink']);

EntireKnockoutAsset::register($this);

$rules = ArrayHelper::map(
    Rule2::find()
        ->where(['<>', 'key', 'nawabari'])
        ->orderBy(['id' => SORT_ASC])
        ->asArray()
        ->all(),
    'key',
    function (array $row): string {
        return Yii::t('app-rule2', $row['name']);
    }
);

$maps = ArrayHelper::map(
    Map2::find()
        ->where(['and',
            ['<>', 'key', 'mystery'],
            ['not', ['like', 'key', 'mystery_%', false]],
        ])
        ->asArray()
        ->all(),
    'key',
    function (array $row): string {
        return Yii::t('app-map2', $row['name']);
    }
);
asort($maps);

// ルール別の合計データを作成する {{{
$_total = [];
foreach ($rules as $_key => $_name) {
  $_total[$_key] = [
    'battle' => 0,
    'ko' => 0,
  ];
}
foreach ($data as $_map) {
  foreach ($_map as $_key => $_value) {
    $_total[$_key]['battle'] += (int)$_value['battles'];
    $_total[$_key]['ko'] += (int)$_value['knockouts'];
  }
}
// }}}

$this->registerCss(Html::renderCss([
  'table' => [
    'min-width' => sprintf('%dpx', 220 * (count($rules) + 1)),
  ],
  'th,td' => [
    'width' => sprintf('%.f%%', 100 / (count($rules) + 1)),
  ],
]));
?>
<div class="container">
  <h1>
    <?= Html::encode($title) . "\n" ?>
  </h1>
  <?= AdWidget::widget() . "\n" ?>
  <?= SnsWidget::widget() . "\n" ?>
  <p>
    <?= Html::encode(Yii::t('app', 'Excluded: Private Battles')) . "\n" ?>
  </p>
  <ul class="nav nav-tabs">
    <li class="active"><a>Splatoon 2</a></li>
    <li><?= Html::a('Splatoon', ['entire/knockout']) ?></li>
  </ul>
  <?php $_form = ActiveForm::begin([
      'action' => ['entire/knockout2'],
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
    <?= $_form->field($form, 'lobby')
      ->label(false)
      ->dropDownList([
        ''          => Yii::t('app-rule2', 'Any Lobby'),
        'standard'  => Yii::t('app-rule2', 'Ranked Battle (Solo)'),
        'squad'     => Yii::t('app-rule2', 'League Battle'),
        'squad_2'   => '┣ ' . Yii::t('app-rule2', 'League Battle (Twin)'),
        'squad_4'   => '┗ ' . Yii::t('app-rule2', 'League Battle (Quad)'),
      ], [
        'onchange' => 'document.getElementById("filter-form").submit()',
      ]) . "\n"
    ?>
    <?= $_form->field($form, 'rank')
      ->label(false)
      ->dropDownList(array_merge(
        ['' => Yii::t('app-rank2', 'Any Rank')],
        ArrayHelper::map(
          RankGroup2::find()
            ->orderBy(['rank' => SORT_DESC])
            ->asArray()
            ->all(),
          'key',
          function (array $group) : string {
            return Yii::t('app-rank2', $group['name']);
          }
        )
      ), [
        'onchange' => 'document.getElementById("filter-form").submit()',
      ]) . "\n"
    ?>
  <?php ActiveForm::end(); echo "\n"; ?>
  <div class="table-responsive table-responsive-force">
    <table class="table table-condensed graph-container">
      <thead>
        <tr>
<?php $_width = (100 / (count($rules) + 1)) ?>
          <?= Html::tag('th', '', [
            'style' => [
              'width' => $_width . '%',
              'min-width' => '200px',
            ],
          ]) . "\n" ?>
<?php foreach ($rules as $_key => $_name) { ?>
          <?= Html::tag('th', Html::encode($_name), [
            'style' => [
              'width' => $_width . '%',
              'min-width' => '200px',
            ],
          ]) . "\n" ?>
<?php } ?>
        </tr>
      </thead>
      <tbody>
        <tr>
          <th>
            <?= $this->render('_knockout_legends') . "\n" ?>
          </th>
<?php foreach ($rules as $_key => $_name) { ?>
          <td>
<?php if ($_total[$_key]['battle'] > 0) { ?>
            <?= Html::tag('div', '', [
              'class' => 'pie-flot-container',
              'data' => [
                'json' => Json::encode($_total[$_key]),
              ],
            ]) . "\n" ?>
<?php } ?>
          </td>
<?php } ?>
        </tr>
<?php foreach ($maps as $_mapKey => $_mapName) { ?>
        <tr>
          <th>
            <?= Html::encode($_mapName) ?><br>
            <?= Spl2Stage::img('daytime', $_mapKey, ['class' => 'map-image']) . "\n" ?>
          </th>
<?php foreach ($rules as $_ruleKey => $_ruleName) { ?>
          <td>
<?php $_data = $data[$_mapKey][$_ruleKey] ?? null ?>
<?php if ($_data && ($_data['battles'] ?? 0) > 0) { ?>
            <?= Html::tag('div', '', [
              'class' => 'pie-flot-container',
              'data' => [
                'json' => Json::encode([
                    'battle' => (int)$_data['battles'],
                    'ko' => (int)$_data['knockouts'],
                ]),
              ],
            ]) . "\n" ?>
<?php $_t = (int)round($_data['avg_game_time']); ?>
<?php if ($_t > 0) { ?>
            <?= Html::tag(
              'p',
              Html::encode(Yii::t(
                'app',
                'Avg. game in {time}',
                [
                  'time' => sprintf('%d:%02d', floor($_t / 60), $_t % 60),
                ]
              )),
              [
                'class' => 'text-center small m-0',
              ]
            ) . "\n" ?>
<?php } ?>
<?php $_t = (int)round($_data['avg_knockout_time'] ?? 300); ?>
<?php if ($_t > 0 && $_t < 300) { ?>
            <?= Html::tag(
              'p',
              Html::encode(Yii::t(
                'app',
                'Avg. K.O. in {time}',
                [
                  'time' => sprintf('%d:%02d', floor($_t / 60), $_t % 60),
                ]
              )),
              [
                'class' => 'text-center small m-0',
              ]
            ) . "\n" ?>
<?php } ?>
<?php } ?>
          </td>
<?php } ?>
        </tr>
<?php } ?>
      </tbody>
    </table>
  </div>
</div>
