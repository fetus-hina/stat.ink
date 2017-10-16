<?php
use app\assets\AppOptAsset;
use app\assets\MapImage2Asset;
use app\components\widgets\AdWidget;
use app\components\widgets\SnsWidget;
use app\models\Map2;
use app\models\Rule2;
use jp3cki\yii2\flot\FlotPieAsset;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;

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
<?php $_mapAsset = MapImage2Asset::register($this) ?>
<?php $this->registerCss('img.map-image{max-width:15em;height:auto}'); ?>
<?php foreach ($maps as $_mapKey => $_mapName): ?>
        <tr>
          <th>
            <?= Html::encode($_mapName) ?><br>
            <?= Html::img(
              Yii::$app->assetManager->getAssetUrl($_mapAsset, sprintf('daytime/%s.jpg', $_mapKey)),
              ['class' => 'map-image']
            ) . "\n" ?>
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
