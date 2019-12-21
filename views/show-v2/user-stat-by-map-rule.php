<?php

use app\assets\StatByMapRuleAsset;
use app\assets\TableResponsiveForceAsset;
use app\components\widgets\AdWidget;
use app\components\widgets\Battle2FilterWidget;
use app\components\widgets\SnsWidget;
use app\components\widgets\UserMiniInfo2;
use app\components\widgets\WinLoseLegend;
use jp3cki\yii2\flot\FlotPieAsset;
use statink\yii2\stages\spl2\Spl2Stage;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;

TableResponsiveForceAsset::register($this);
StatByMapRuleAsset::register($this);

$assetManager = Yii::$app->assetManager;
FlotPieAsset::register($this);

$title = Yii::t('app', "{0}'s Battle Stats (by Mode and Stage)", [$user->name]);
$this->title = sprintf('%s | %s', Yii::$app->name, $title);

$this->registerMetaTag(['name' => 'twitter:card', 'content' => 'summary']);
$this->registerMetaTag(['name' => 'twitter:title', 'content' => $title]);
$this->registerMetaTag(['name' => 'twitter:description', 'content' => $title]);
$this->registerMetaTag(['name' => 'twitter:site', 'content' => '@stat_ink']);
$this->registerMetaTag([
    'name' => 'twitter:image',
    'content' => $user->userIcon->absUrl ?? $user->jdenticonPngUrl,
]);
if ($user->twitter != '') {
    $this->registerMetaTag([
        'name' => 'twitter:creator',
        'content' => sprintf('@%s', $user->twitter),
    ]);
}

$this->registerCss(Html::renderCss([
  '.pie-flot-container .error' => [
    'display' => 'none',
  ],
  'table.graph-container' => [
    'table-layout' => 'fixed',
  ],
  'table.graph-container thead tr:nth-child(1) th' => [
    'width' => '16.667%',
  ],
  'table.graph-container thead tr:nth-child(1) th:nth-child(1)' => [
    'width' => '16.667%',
    'min-width' => '120px',
    'max-width' => '200px',
  ],
]));

$ruleMap = [
    'nawabari' => 'standard-regular-nawabari',
    'area' => 'any-gachi-area',
    'yagura' => 'any-gachi-yagura',
    'hoko' => 'any-gachi-hoko',
    'asari' => 'any-gachi-asari',
];
?>
<div class="container">
  <h1>
    <?= Html::encode($title) . "\n" ?>
  </h1>
  <?= SnsWidget::widget() . "\n" ?>
  <div class="row">
    <div class="col-xs-12 col-sm-8 col-lg-9 table-responsive table-responsive-force">
      <table class="table table-condensed graph-container">
        <thead>
          <tr>
            <th>
              <?= WinLoseLegend::widget() . "\n" ?>
            </th>
<?php foreach ($ruleNames as $ruleKey => $ruleName): ?>
            <th>
              <?= Html::a(
                Html::encode($ruleName),
                ['show-v2/user',
                  'screen_name' => $user->screen_name,
                  'filter' => [
                    'rule' => $ruleMap[$ruleKey],
                  ],
                ]
              ) . "\n" ?>
            </th>
<?php endforeach; ?>
          </tr>
        </thead>
        <tbody>
          <tr>
            <th></th>
<?php foreach ($ruleNames as $ruleKey => $ruleName): ?>
            <td>
              <?= Html::tag(
                'div',
                '',
                [
                  'class' => 'pie-flot-container',
                  'data' => [
                    'json' => Json::encode($data['total'][$ruleKey]),
                    'click-href' => Url::to(['show-v2/user',
                      'screen_name' => $user->screen_name,
                      'filter' => [
                        'rule' => $ruleMap[$ruleKey],
                      ],
                    ]),
                  ],
                ]
              ) . "\n" ?>
            </td>
<?php endforeach; ?>
          </tr>
<?php foreach ($mapNames as $mapKey => $mapName): ?>
          <tr>
            <th>
              <?= Html::a(
                implode('<br>', [
                  Html::encode($mapName),
                  Spl2Stage::img('daytime', $mapKey, ['style' => [
                    'max-width' => '100%',
                  ]]),
                ]),
                ['show-v2/user',
                  'screen_name' => $user->screen_name,
                  'filter' => [
                    'map' => $mapKey,
                  ],
                ]
              ) . "\n" ?>
            </th>
<?php foreach ($ruleNames as $ruleKey => $ruleName): ?>
            <td>
              <?= Html::tag(
                'div',
                '',
                [
                  'class' => 'pie-flot-container',
                  'data' => [
                    'json' => Json::encode($data[$mapKey][$ruleKey]),
                    'click-href' => Url::to(['show-v2/user',
                      'screen_name' => $user->screen_name,
                      'filter' => [
                        'rule' => $ruleMap[$ruleKey],
                        'map' => $mapKey,
                      ],
                    ]),
                  ],
                ]
              ) . "\n" ?>
            </td>
<?php endforeach; ?>
          </tr>
<?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <div class="col-xs-12 col-sm-4 col-lg-3">
      <?= Battle2FilterWidget::widget([
        'route' => 'show-v2/user-stat-by-map-rule',
        'screen_name' => $user->screen_name,
        'filter' => $filter,
        'action' => 'summarize',
        'rule' => false,
        'map' => false,
        'result' => false,
      ]) . "\n" ?>
      <?= UserMiniInfo2::widget(['user' => $user]) . "\n" ?>
      <?= AdWidget::widget() . "\n" ?>
    </div>
  </div>
</div>
