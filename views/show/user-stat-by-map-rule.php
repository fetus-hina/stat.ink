<?php

/**
 * @copyright Copyright (C) 2019-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\assets\StatByMapRuleAsset;
use app\assets\TableResponsiveForceAsset;
use app\components\widgets\AdWidget;
use app\components\widgets\BattleFilterWidget;
use app\components\widgets\Icon;
use app\components\widgets\SnsWidget;
use app\components\widgets\WinLoseLegend;
use app\models\User;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\View;

/**
 * @var User $user
 * @var View $this
 */

$title = Yii::t('app', "{name}'s Battle Stats (by Mode and Stage)", ['name' => $user->name]);
$this->title = implode(' | ', [Yii::$app->name, $title]);

$this->registerMetaTag(['name' => 'twitter:card', 'content' => 'summary']);
$this->registerMetaTag(['name' => 'twitter:title', 'content' => $title]);
$this->registerMetaTag(['name' => 'twitter:description', 'content' => $title]);
$this->registerMetaTag(['name' => 'twitter:site', 'content' => '@stat_ink']);
$this->registerMetaTag(['name' => 'twitter:image', 'content' => $user->iconUrl]);
if ($user->twitter != '') {
  $this->registerMetaTag(['name' => 'twitter:creator', 'content' => '@' . $user->twitter]);
}

StatByMapRuleAsset::register($this);

$this->registerCss(implode('', [
  '.pie-flot-container{height:200px}',
  '.pie-flot-container .error{display:none}',
  '.graph-container thead tr:nth-child(1) th{width:20%;min-width:150px}',
]));
?>
<div class="container">
  <h1><?= Html::encode($title) ?></h1>

  <?= SnsWidget::widget() . "\n" ?>

  <p>
    <?= Html::a(
      implode(' ', [
        Html::encode(Yii::t('app', 'Details')),
        Icon::nextPage(),
      ]),
      ['show/user-stat-by-map-rule-detail', 'screen_name' => $user->screen_name],
      ['class' => 'btn btn-success']
    ) . "\n" ?>
  </p>

  <div class="row">
<?php TableResponsiveForceAsset::register($this) ?>
    <div class="col-xs-12 col-sm-8 col-md-8 col-lg-9 table-responsive table-responsive-force">
      <table class="table table-condensed graph-container">
        <thead>
          <tr>
            <th><?= WinLoseLegend::widget() ?></th>
            <?= implode('', array_map(
              function (string $key, string $name) use ($user): string {
                return Html::tag(
                  'th',
                  Html::a(
                    Html::encode($name),
                    ['show/user',
                      'screen_name' => $user->screen_name,
                      'filter' => [
                        'rule' => $key,
                      ],
                    ]
                  )
                );
              },
              array_keys($ruleNames),
              array_values($ruleNames)
            )) . "\n" ?>
          </tr>
        </thead>
        <tbody>
<?php foreach ($mapNames as $mapKey => $mapName) { ?>
          <tr>
            <th><?= Html::a(
              Html::encode($mapName),
              ['show/user',
                'screen_name' => $user->screen_name,
                'filter' => [
                  'map' => $mapKey,
                ],
              ]
            ) ?></th>
            <?= implode('', array_map(
              function (string $ruleKey, string $ruleName) use ($mapKey, $user, $data): string {
                return Html::tag(
                  'td',
                  Html::tag('div', '', [
                    'class' => 'pie-flot-container',
                    'data' => [
                      'json' => Json::encode($data[$mapKey][$ruleKey]),
                      'click-href' => Url::to(['show/user',
                        'screen_name' => $user->screen_name,
                        'filter' => [
                          'rule' => $ruleKey,
                          'map' => $mapKey,
                        ],
                      ]),
                    ],
                  ])
                );
              },
              array_keys($ruleNames),
              array_values($ruleNames)
            )) . "\n" ?>
          </tr>
<?php } ?>
        </tbody>
      </table>
    </div>
    <div class="col-xs-12 col-sm-4 col-md-4 col-lg-3">
      <?= BattleFilterWidget::widget([
        'route' => 'show/user-stat-by-map-rule',
        'screen_name' => $user->screen_name,
        'filter' => $filter,
        'action' => 'summarize',
        'rule' => false,
        'map' => false,
        'result' => false,
      ]) . "\n" ?>
      <?= $this->render("//includes/user-miniinfo", ["user" => $user]) . "\n" ?>
      <?= AdWidget::widget() . "\n" ?>
    </div>
  </div>
</div>
