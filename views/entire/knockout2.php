<?php
use app\assets\AppOptAsset;
use app\components\widgets\AdWidget;
use app\components\widgets\SnsWidget;
use app\models\Map2;
use app\models\RankGroup2;
use app\models\Rule2;
use jp3cki\yii2\flot\FlotPieAsset;
use statink\yii2\stages\spl2\Spl2Stage;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\widgets\ActiveForm;

$title = Yii::t('app', 'Knockout Ratio');
$this->title = Yii::$app->name . ' | ' . $title;

$this->registerMetaTag(['name' => 'twitter:card', 'content' => 'summary']);
$this->registerMetaTag(['name' => 'twitter:title', 'content' => $title]);
$this->registerMetaTag(['name' => 'twitter:description', 'content' => $title]);
$this->registerMetaTag(['name' => 'twitter:site', 'content' => '@stat_ink']);

FlotPieAsset::register($this);
AppOptAsset::register($this)
    ->registerJsFile($this, 'knockout.js');
$this->registerCss('.pie-flot-container{height:200px}.pie-flot-container .error{display:none}');

$rules = ArrayHelper::map(
    Rule2::find()
        ->where(['<>', 'key', 'nawabari'])
        ->orderBy(['id' => SORT_ASC])
        ->asArray()
        ->all(),
    'key',
    function (array $row) : string {
        return Yii::t('app-rule2', $row['name']);
    }
);

$maps = ArrayHelper::map(
    Map2::find()->where(['<>', 'key', 'mystery'])->asArray()->all(),
    'key',
    function (array $row) : string {
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
    <li class="active"><a href="javascript:;">Splatoon 2</a></li>
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
<?php $this->registerCss('img.map-image{max-width:15em;height:auto}'); ?>
<?php foreach ($maps as $_mapKey => $_mapName): ?>
        <tr>
          <th>
            <?= Html::encode($_mapName) ?><br>
            <?= Spl2Stage::img('daytime', $_mapKey, ['class' => 'map-image']) . "\n" ?>
          </th>
<?php foreach ($rules as $_ruleKey => $_ruleName): ?>
          <td>
<?php $_data = $data[$_mapKey][$_ruleKey] ?? null ?>
<?php if ($_data && ($_data['battles'] ?? 0) > 0): ?>
            <?= Html::tag('div', '', [
              'class' => 'pie-flot-container',
              'data' => [
                'json' => Json::encode([
                    'battle' => (int)$_data['battles'],
                    'ko' => (int)$_data['knockouts'],
                ]),
              ],
            ]) . "\n" ?>
<?php $_t = (int)round($_data['avg_knockout_time'] ?? 300); ?>
<?php if ($_t > 0 && $_t < 300): ?>
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
                'style' => ['font-size' => '0.8em', 'margin' => '0',],
                'class' => 'text-center',
              ]
            ) . "\n" ?>
<?php endif; ?>
<?php endif; ?>
          </td>
<?php endforeach; ?>
        </tr>
<?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
