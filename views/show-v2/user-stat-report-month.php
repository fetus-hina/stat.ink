<?php

/**
 * @copyright Copyright (C) 2017-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\assets\Spl2WeaponAsset;
use app\assets\UserStatReportAsset;
use app\components\widgets\AdWidget;
use app\components\widgets\Icon;
use app\components\widgets\SnsWidget;
use app\models\Battle2;
use app\models\User;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var User $user
 * @var View $this
 */

$title = Yii::t('app', "{name}'s Battle Report", ['name' => $user->name]);
$this->title = Yii::$app->name . ' | ' . $title;

$this->registerMetaTag(['name' => 'twitter:card', 'content' => 'summary']);
$this->registerMetaTag(['name' => 'twitter:title', 'content' => $title]);
$this->registerMetaTag(['name' => 'twitter:description', 'content' => $title]);
$this->registerMetaTag(['name' => 'twitter:site', 'content' => '@stat_ink']);
$this->registerMetaTag(['name' => 'twitter:image', 'content' => $user->getIconUrl()]);
if ($user->twitter != '') {
  $this->registerMetaTag(['name' => 'twitter:creator', 'content' => '@' . $user->twitter]);
}
if ($next) {
  $this->registerLinkTag(['rel' => 'next', 'href' => $next]);
}
if ($prev) {
  $this->registerLinkTag(['rel' => 'prev', 'href' => $prev]);
}
UserStatReportAsset::register($this);
$weapons = Spl2WeaponAsset::register($this);
?>
<div class="container">
  <h1><?= Yii::t('app', "{name}'s Battle Report", [
    'name' => Html::a(
      Html::encode($user->name),
      ['show-v2/user', 'screen_name' => $user->screen_name]
    ),
  ]) ?></h1>

  <?= AdWidget::widget() . "\n" ?>
  <?= SnsWidget::widget() . "\n" ?>

<?php if ($next || $prev) { ?>
  <div class="row mb-3">
    <div class="col-xs-6">
<?php if ($prev) { ?>
      <?= Html::a(
        implode(' ', [
          Icon::prevPage(),
          Html::encode(Yii::t('app', 'Prev. Month')),
        ]),
        $prev,
        ['class' => 'btn btn-default']
      ) . "\n" ?>
<?php } ?>
    </div>
    <div class="col-xs-6 pull-right text-right">
<?php if ($next) { ?>
      <?= Html::a(
        implode(' ', [
          Html::encode(Yii::t('app', 'Next Month')),
          Icon::nextPage(),
        ]),
        $next,
        ['class' => 'btn btn-default']
      ) . "\n" ?>
<?php } ?>
    </div>
  </div>
<?php } ?>
  <div class="table-responsive">
    <table class="table table-striped table-condensed">
      <thead>
        <tr>
          <th></th>
<?php $_list = [
    'Lobby', 'Mode', 'Team ID', 'Stage', 'Weapon', 'Battles', 'Win %',
    'k', 'd', 'k+a', 'sp', 'KR'
]; ?>
<?php foreach ($_list as $_value) { ?>
          <?= Html::tag('th', Html::encode(Yii::t('app', $_value))) . "\n" ?>
<?php } ?>
        </tr>
      <tbody>
<?php $_lastDate = null ?>
<?php foreach ($list as $row) { ?>
<?php if ($_lastDate !== $row['date']) { ?>
<?php $_lastDate = $row['date'] ?>
        <tr class="row-date">
          <?= Html::tag(
            'th',
            Html::encode(
              Yii::$app->formatter->asDate($row['date'], 'full')
            ),
            [
              'id' => 'date-' . $row['date'],
              'colspan' => 13,
            ]
          ) . "\n" ?>
        </tr>
<?php } ?>
        <tr>
          <td>
            <?= Html::a(
              Icon::search(),
              ['show-v2/user',
                'screen_name' => $user->screen_name,
                'filter' => [
                  'rule' => (function () use ($row) {
                    return sprintf(
                      '%s-%s-%s',
                      $row['lobby_key'],
                      $row['mode_key'],
                      $row['rule_key']
                    );
                  })(),
                  'map' => $row['map_key'],
                  'weapon' => $row['weapon_key'],
                  'term' => 'term',
                  'term_from' => $row['date'] . ' 00:00:00',
                  'term_to' => $row['date'] . ' 23:59:59',
                  'timezone' => Yii::$app->timeZone,
                ],
              ]
            ) . "\n" ?>
          </td>
          <td><?= Html::encode((function () use ($row) : string {
            switch ($row['lobby_key']) {
              case 'standard':
                if ($row['mode_key'] === 'fest') {
                  if (version_compare($row['version_tag'], '4.0.0', '>=')) {
                    return Yii::t('app-rule2', 'Splatfest (Pro)');
                  } else {
                    return Yii::t('app-rule2', 'Splatfest (Solo)');
                  }
                } elseif ($row['rule_key'] === 'nawabari') {
                  return Yii::t('app-rule2', 'Regular Battle');
                } else {
                  return Yii::t('app-rule2', 'Ranked Battle');
                }
                break;

              case 'fest_normal':
                return Yii::t('app-rule2', 'Splatfest (Normal)');

              case 'squad_2':
                return Yii::t('app-rule2', 'League Battle (Twin)');

              case 'squad_4':
                if ($row['mode_key'] === 'fest') {
                  return Yii::t('app-rule2', 'Splatfest (Team)');
                } else {
                  return Yii::t('app-rule2', 'League Battle (Quad)');
                }
                break;
              
              default:
                return $row['lobby_key'];
            }
          })()) ?></td>
          <td><?= Html::encode($row['rule_name']) ?></td>
          <td><?=
            ($row['team_id'] == '')
              ? ''
              : Html::a(
                implode('', [
                  Html::img(
                    Battle2::teamIcon($row['team_id']),
                    [
                      'style' => [
                        'width' => 'auto',
                        'height' => '1.5em',
                      ],
                    ]
                  ),
                  Html::tag('code', Html::encode($row['team_id'])),
                ]),
                ['show-v2/user',
                  'screen_name' => $user->screen_name,
                  'filter' => [
                    'filter' => "team:{$row['team_id']}",
                  ],
                ]
              )
          ?></td>
          <td><?= Html::encode($row['map_name']) ?></td>
          <td><?= implode(' ', [
            Html::img($weapons->getIconUrl($row['weapon_key']), [
              'style' => [
                'height' => '1.5em',
              ],
            ]),
            Html::encode($row['weapon_name']),
          ]) ?></td>
          <td class="text-right"><?= Html::encode(Yii::$app->formatter->asInteger((int)$row['battles'])) ?></td>
          <td class="text-right"><?= Html::encode(
            ($row['battles'] < 1)
              ? ''
              : Yii::$app->formatter->asPercent($row['wins'] / $row['battles'], 1)
          ) ?></td>
          </td>
<?php
$_fmt = function (string $key, string $item) use ($row) : string {
  $value = $row[$item . '_' . $key];
  return ($value === null) ? '' : Yii::$app->formatter->asDecimal($value, 1);
}
?>
<?php foreach (['kill', 'death', 'kill_or_assist', 'special'] as $_key) { ?>
          <td class="text-right">
            <?= ($row['avg_' . $_key] === null) ? '' : Html::tag(
              'span',
              Html::encode(Yii::$app->formatter->asDecimal($row['avg_' . $_key], 1)),
              [
                'class' => 'auto-tooltip',
                'title' => Yii::t(
                  'app',
                  'max={max} min={min} average={avg} median={median} mode={mode}',
                  [
                    'max'    => $_fmt($_key, 'max'),
                    'min'    => $_fmt($_key, 'min'),
                    'avg'    => $_fmt($_key, 'avg'),
                    'median' => $_fmt($_key, 'med'),
                    'mode'   => $_fmt($_key, 'mod'),
                  ]
                ),
              ]
            ) . "\n" ?>
          </td>
<?php } ?>
          <td class="text-right"><?= Html::encode(
            (function () use ($row) : string {
              if ($row['deaths_for_ratio'] == 0) {
                if ($row['kills_for_ratio'] == 0) {
                  return '';
                } else {
                  return Yii::$app->formatter->asDecimal(99.99, 2);
                }
              }
              return Yii::$app->formatter->asDecimal($row['kills_for_ratio'] / $row['deaths_for_ratio'], 2);
            })()
          ) ?></td>
          </td>
        </tr>
<?php } ?>
<?php if (!$list) { ?>
        <tr>
          <td colspan="13">
            <?= Html::encode(Yii::t('app', 'There are no data.')) . "\n" ?>
          </td>
        </tr>
<?php } ?>
      </tbody>
    </table>
  </div>
</div>
