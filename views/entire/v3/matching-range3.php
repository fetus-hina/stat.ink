<?php

/**
 * @copyright Copyright (C) 2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

use app\components\helpers\OgpHelper;
use app\components\widgets\AdWidget;
use app\components\widgets\Icon;
use app\components\widgets\SnsWidget;
use app\models\Rule3;
use app\models\Season3;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var Rule3|null $rule
 * @var Season3 $season
 * @var Season3[] $seasons
 * @var View $this
 * @var array<int, Rule3> $rules
 * @var array<string, array{key: string, label: string, color: string}> $series
 * @var array<string, array<string, int>> $spread
 * @var array<string, array<string, int>> $teamMr
 * @var array<string, array<string, array{battles: int, wins: int|null}>> $mrDiff
 * @var array<string, array<string, array{battles: int, wins: int|null}>> $spreadDiff
 * @var callable(Rule3|null): string $ruleUrl
 * @var callable(Season3): string $seasonUrl
 */

$minSamples = 100;

$title = vsprintf('%s (MR)', [Yii::t('app-xmatch3', 'Matching Range')]);
$this->title = $title . ' | ' . Yii::$app->name;

OgpHelper::default($this, title: $title);

$battleCounts = fn (array $rows): array => array_map(
  fn (array $values): array => array_map(
    fn (array $row): int => $row['battles'],
    $values,
  ),
  $rows,
);

$sections = [
  [
    'id' => 'team-mr',
    'title' => Yii::t('app-entire', 'Team MR'),
    'description' => Yii::t('app-entire', 'Team MR: the average of the MR of the 4 players.'),
    'view' => 'matching-range3/distribution',
    'params' => [
      'rows' => $teamMr,
      'unit' => 0.025,
      'xLabel' => Yii::t('app-entire', 'Team MR'),
      'yLabel' => Yii::t('app-entire', '% of teams'),
    ],
  ],
  [
    'id' => 'mr-diff',
    'title' => Yii::t('app-entire', 'MR Difference between Teams'),
    'description' => Yii::t('app-entire', 'The difference of the team MR between the two teams in each battle.'),
    'view' => 'matching-range3/distribution',
    'params' => [
      'rows' => $battleCounts($mrDiff),
      'unit' => 0.025,
      'xLabel' => Yii::t('app-entire', 'MR Difference'),
      'yLabel' => Yii::t('app-entire', '% of battles'),
    ],
  ],
  [
    'id' => 'mr-diff-win',
    'title' => Yii::t('app-entire', 'MR Difference and Win %'),
    'description' => Yii::t('app-entire', 'The win rate of the team with the higher team MR.'),
    'view' => 'matching-range3/win-rate',
    'params' => [
      'rows' => $mrDiff,
      'unit' => 0.025,
      'width' => 0.25,
      'overflow' => 2.0,
      'xLabel' => Yii::t('app-entire', 'MR Difference'),
      'yLabel' => Yii::t('app-entire', 'Win % of the higher-MR team'),
    ],
  ],
  [
    'id' => 'spread',
    'title' => Yii::t('app-entire', 'In-team MR Spread'),
    'description' => Yii::t('app-entire', 'In-team MR spread: the difference between the maximum and the minimum MR of the 4 players.'),
    'view' => 'matching-range3/distribution',
    'params' => [
      'rows' => $spread,
      'unit' => 0.1,
      'xLabel' => Yii::t('app-entire', 'In-team MR Spread'),
      'yLabel' => Yii::t('app-entire', '% of teams'),
    ],
  ],
  [
    'id' => 'spread-diff-win',
    'title' => Yii::t('app-entire', 'In-team MR Spread Difference and Win %'),
    'description' => Yii::t('app-entire', 'The win rate of the team with the wider in-team MR spread.'),
    'view' => 'matching-range3/win-rate',
    'params' => [
      'rows' => $spreadDiff,
      'unit' => 0.1,
      'width' => 1.0,
      'overflow' => 8.0,
      'xLabel' => Yii::t('app-entire', 'In-team MR Spread Difference'),
      'yLabel' => Yii::t('app-entire', 'Win % of the wider-spread team'),
    ],
  ],
];

?>
<div class="container">
  <h1>
    <?= Html::encode($title) . "\n" ?>
  </h1>

  <?= AdWidget::widget() . "\n" ?>
  <?= SnsWidget::widget() . "\n" ?>

  <div class="mb-3">
    <div class="mb-1">
      <?= $this->render(
        'includes/season-selector',
        compact('season', 'seasonUrl', 'seasons'),
      ) . "\n" ?>
    </div>
    <?= $this->render('matching-range3/rule-tabs', compact('rule', 'rules', 'ruleUrl')) . "\n" ?>
  </div>

  <p class="mb-1">
    <?= Yii::t('app-entire', 'Aggregated: {rules}', [
      'rules' => implode(', ', [
        Icon::s3LobbyX() . ' ' . Html::encode(
          vsprintf('%s (%s)', [
            Yii::t('app-lobby3', 'X Battle'),
            Yii::t('app-entire', 'v{version} or later', ['version' => '11.1.0']),
          ]),
        ),
        Icon::s3LobbyBankara() . ' ' . Html::encode(
          vsprintf('%s (%s)', [
            Yii::t('app-lobby3', 'Anarchy Battle (Series)'),
            Yii::t('app-entire', 'v{version} or later', ['version' => '12.0.0']),
          ]),
        ),
      ]),
    ]) . "\n" ?>
  </p>
  <ul class="mb-3">
    <li><?= Html::encode(Yii::t('app-entire', 'X Battle is classified by the X Power of the battle uploader.')) ?></li>
  </ul>

  <div class="alert alert-danger mb-3">
    <?= Html::encode(
      Yii::t('app', 'This data is based on {siteName} users and differs significantly from overall game statistics.', [
        'siteName' => Yii::$app->name,
      ]),
    ) . "\n" ?>
  </div>

  <div class="alert alert-info mb-3">
    <?= implode(' ', [
      Html::encode(Yii::t('app-entire', 'Matching Range (MR) is a value set for each main weapon, roughly corresponding to its range.')),
      Html::encode(Yii::t('app-entire', 'In X Battle and Anarchy Battle (Series), players are divided into teams so that the MR of both teams are close.')),
      Html::a(
        Html::encode(Yii::t('app-entire', 'MR of each weapon')),
        ['api-info/weapon3'],
        ['class' => 'alert-link'],
      ),
    ]) . "\n" ?>
  </div>

  <div class="alert alert-info mb-3">
    <?= Html::encode(
      Yii::t('app-statistics', 'The width of the histogram bins is automatically adjusted by Scott\'s rule-based algorithm.'),
    ) . "\n" ?>
  </div>

<?php foreach ($sections as $section) { ?>
  <?= Html::tag('h2', Html::encode($section['title']), ['id' => $section['id']]) . "\n" ?>
  <p><?= Html::encode($section['description']) ?></p>
  <?= $this->render($section['view'], [
    ...$section['params'],
    'id' => 'chart-' . $section['id'],
    'minSamples' => $minSamples,
    'series' => $series,
  ]) . "\n" ?>
<?php } ?>
</div>
