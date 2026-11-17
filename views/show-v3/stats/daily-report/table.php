<?php

/**
 * @copyright Copyright (C) 2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

use app\actions\show\v3\stats\DailyReportAction;
use app\components\widgets\Icon;
use app\models\Battle3FilterForm;
use app\models\Lobby3;
use app\models\Map3;
use app\models\Rule3;
use app\models\User;
use app\models\Weapon3;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var User $user
 * @var View $this
 * @var list<array{
 *   date: string,
 *   summary: array{
 *     battles: int,
 *     wins: int,
 *     kills_for_ratio: int,
 *     deaths_for_ratio: int,
 *     stats: array<string, array{avg: ?float, min: ?float, max: ?float, med: ?float, mod: ?float, sd: ?float}>
 *   },
 *   rows: list<array{
 *     lobby: Lobby3,
 *     rule: Rule3,
 *     map: Map3,
 *     weapon: Weapon3,
 *     summary: array{
 *       battles: int,
 *       wins: int,
 *       kills_for_ratio: int,
 *       deaths_for_ratio: int,
 *       stats: array<string, array{avg: ?float, min: ?float, max: ?float, med: ?float, mod: ?float, sd: ?float}>
 *     }
 *   }>
 * }> $list
 */

$fmt = Yii::$app->formatter;
$tz = new DateTimeZone(Yii::$app->timeZone);

$headers = [
  '',
  Yii::t('app', 'Lobby'),
  Yii::t('app', 'Mode'),
  Yii::t('app', 'Stage'),
  Yii::t('app', 'Weapon'),
  Yii::t('app', 'Battles'),
  Yii::t('app-results', 'Win %'),
  ...array_column(DailyReportAction::getStatsColumns(), 'label'),
  Yii::t('app-results', 'KR'),
];
$colspan = count($headers);

$shortLabel = fn (string $short, string $full): string => $short === $full
  ? Html::encode($full)
  : Html::tag('span', Html::encode($short), [
    'class' => 'auto-tooltip',
    'title' => $full,
  ]);

/**
 * @param array<string, string> $filter
 */
$searchLink = fn (string $date, array $filter): string => Html::a(
  Icon::search(),
  [
    'show-v3/user',
    'screen_name' => $user->screen_name,
    'f' => array_merge($filter, [
      'result' => Battle3FilterForm::RESULT_WIN_OR_LOSE,
      'term' => 'term',
      'term_from' => $date . ' 00:00:00',
      'term_to' => $date . ' 23:59:59',
    ]),
  ],
);

?>
<table class="table table-striped table-condensed">
  <thead>
    <tr>
<?php foreach ($headers as $header) { ?>
      <?= Html::tag('th', Html::encode($header)) . "\n" ?>
<?php } ?>
    </tr>
  </thead>
  <tbody>
<?php foreach ($list as $day) { ?>
    <tr class="row-date">
      <?= Html::tag(
        'th',
        implode(' ', [
          $searchLink($day['date'], ['lobby' => Battle3FilterForm::LOBBY_NOT_PRIVATE]),
          Html::encode($fmt->asDate(new DateTimeImmutable($day['date'] . 'T00:00:00', $tz), 'full')),
        ]),
        [
          'class' => 'nobr',
          'colspan' => '5',
          'id' => 'date-' . $day['date'],
        ],
      ) . "\n" ?>
      <?= $this->render('summary-cells', ['summary' => $day['summary']]) ?>
    </tr>
<?php foreach ($day['rows'] as $row) { ?>
    <tr>
      <td><?= $searchLink($day['date'], [
        'lobby' => $row['lobby']->key,
        'rule' => $row['rule']->key,
        'map' => $row['map']->key,
        'weapon' => $row['weapon']->key,
      ]) ?></td>
      <td class="nobr"><?= implode(' ', [
        (string)Icon::s3Lobby($row['lobby']),
        $shortLabel(
          Yii::t('app-lobby3', $row['lobby']->short_name),
          Yii::t('app-lobby3', $row['lobby']->name),
        ),
      ]) ?></td>
      <td class="nobr"><?= implode(' ', [
        (string)Icon::s3Rule($row['rule']),
        $shortLabel(
          Yii::t('app-rule3', $row['rule']->short_name),
          Yii::t('app-rule3', $row['rule']->name),
        ),
      ]) ?></td>
      <td class="nobr"><?= $shortLabel(
        Yii::t('app-map3', $row['map']->short_name),
        Yii::t('app-map3', $row['map']->name),
      ) ?></td>
      <td class="nobr"><?= implode(' ', [
        (string)Icon::s3Weapon($row['weapon']),
        $shortLabel(
          Yii::$app->weaponShortener->get(Yii::t('app-weapon3', $row['weapon']->name)),
          Yii::t('app-weapon3', $row['weapon']->name),
        ),
      ]) ?></td>
      <?= $this->render('summary-cells', ['summary' => $row['summary']]) ?>
    </tr>
<?php } ?>
<?php } ?>
<?php if (!$list) { ?>
    <tr>
      <?= Html::tag('td', Html::encode(Yii::t('app', 'There are no data.')), [
        'colspan' => (string)$colspan,
      ]) . "\n" ?>
    </tr>
<?php } ?>
  </tbody>
</table>
