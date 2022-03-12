<?php

declare(strict_types=1);

use app\assets\EntireFestpower2Asset;
use app\assets\TableResponsiveForceAsset;
use app\components\helpers\Html;
use app\components\widgets\AdWidget;
use app\components\widgets\Alert;
use app\components\widgets\FA;
use app\components\widgets\SnsWidget;
use yii\data\ArrayDataProvider;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\web\View;
use yii\widgets\ActiveForm;

/**
 * @var View $this
 * @var array $data
 * @var float $avgAll
 * @var float $avgMistake
 * @var float $avgNormal
 * @var float $medianAll
 * @var float $medianMistake
 * @var float $medianNormal
 * @var float $q1All
 * @var float $q1Mistake
 * @var float $q1Normal
 * @var float $q3All
 * @var float $q3Mistake
 * @var float $q3Normal
 * @var float $stddevAll
 * @var float $stddevMistake
 * @var float $stddevNormal
 * @var int $totalBattles
 * @var int $totalMistakeBattles
 */

EntireFestpower2Asset::register($this);
TableResponsiveForceAsset::register($this);

$title = Yii::t('app-festpower2', 'Splatfest Power vs Win %');
$this->title = Yii::$app->name . ' | ' . $title;

$this->registerMetaTag(['name' => 'twitter:card', 'content' => 'summary']);
$this->registerMetaTag(['name' => 'twitter:title', 'content' => $title]);
$this->registerMetaTag(['name' => 'twitter:description', 'content' => $title]);
$this->registerMetaTag(['name' => 'twitter:site', 'content' => '@stat_ink']);

function winRateColumn(int $diff, int $battles, int $win): ?string
{
  $f = Yii::$app->formatter;
  if ($diff === 0) {
    return $f->asPercent(0.5, 2);
  }

  if ($battles < 1) {
    return null;
  }

  $error = getErrorPoint($battles, $win);
  if ($error === null) {
    return $f->asPercent($win / $battles, 2);
  }

  return vsprintf('%s±%s%%', [
    $f->asDecimal($win / $battles * 100, 2),
    $f->asDecimal($error, 2),
  ]);
}

function getErrorPoint(int $battles, int $wins): ?float
{
  $stdError = calcError($battles, $wins);
  return ($stdError === null)
    ? null
    : $stdError * 100 * 3;
}

function calcError(int $battles, int $wins): ?float
{
  if ($battles < 1 || $wins < 0) {
    return null;
  }

  // ref. http://lfics81.techblog.jp/archives/2982884.html
  $winRate = $wins / $battles;
  $s = sqrt(($battles / ($battles - 1.5)) * $winRate * (1.0 - $winRate));
  return $s / sqrt($battles);
}

function getWinPctChartData(int $diff, int $battles, int $wins): array
{
  if ($diff === 0) {
    return [0, 50, 0];
  }

  if ($battles < 1) {
    return [$diff, null, null];
  }

  $error = getErrorPoint($battles, $wins);
  $winPct = 100 * $wins / $battles;
  return [$diff, $winPct, $error];
}

$chartData = [
  'all_battles' => array_values(array_map(
    function (array $row): array {
      return [(int)$row['diff'], (int)$row['battles']];
    },
    $data
  )),
  'all_pct' => array_values(array_map(
    function (array $row): array {
      return getWinPctChartData(
        $row['diff'],
        $row['battles'],
        $row['higher_wins']
      );
    },
    $data
  )),
  'normal_battles' => array_values(array_map(
    function (array $row): array {
      return [
        (int)$row['diff'],
        (int)($row['battles'] - $row['mistake_battles']),
      ];
    },
    $data
  )),
  'normal_pct' => array_values(array_map(
    function (array $row): array {
      return getWinPctChartData(
        $row['diff'],
        $row['battles'] - $row['mistake_battles'],
        $row['higher_wins'] - $row['mistake_higher_wins'],
      );
    },
    $data
  )),
  'mistake_battles' => array_values(array_map(
    function (array $row): array {
      return [(int)$row['diff'], (int)$row['mistake_battles']];
    },
    $data
  )),
  'mistake_pct' => array_values(array_map(
    function (array $row): array {
      return getWinPctChartData(
        $row['diff'],
        $row['mistake_battles'],
        $row['mistake_higher_wins'],
      );
    },
    $data
  )),
];
?>
<div class="container">
  <h1>
    <?= Html::encode($title) . "\n" ?>
  </h1>
  <?= AdWidget::widget() . "\n" ?>
  <?= SnsWidget::widget() . "\n" ?>

  <?= Alert::widget([
    'options' => [
      'class' => 'alert-info',
    ],
    'body' => Yii::t(
      'app-festpower2',
      '"Mistaken": On {date}, <a href="{url}" class="alert-link">Nintendo misconfigured the matching server and ran the fest.</a>',
      [
        'url' => 'https://twitter.com/splatoonjp/status/998369650986569728',
        'date' => Yii::$app->formatter->asDate('2018-05-19', 'long'),
      ]
    ),
  ]) . "\n" ?>
  <table class="table table-bordered w-auto">
    <thead>
      <tr>
        <th></th>
        <th class="text-center"><?= Html::encode(Yii::t('app-festpower2', 'All')) ?></th>
        <th class="text-center"><?= Html::encode(Yii::t('app-festpower2', 'Normal')) ?></th>
        <th class="text-center"><?= Html::encode(Yii::t('app-festpower2', 'Mistaken')) ?></th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <th class="text-right" scope="row"><?= Html::encode(Yii::t('app-festpower2', 'Battles')) ?></th>
        <td class="text-right"><?= Yii::$app->formatter->asInteger((int)$totalBattles) ?></td>
        <td class="text-right"><?= Yii::$app->formatter->asInteger((int)$totalBattles - (int)$totalMistakeBattles) ?></td>
        <td class="text-right"><?= Yii::$app->formatter->asInteger((int)$totalMistakeBattles) ?></td>
      </tr>
      <tr>
        <th class="text-right" scope="row"><?= Html::encode(Yii::t('app-festpower2', 'Average')) ?></th>
        <td class="text-right"><?= Yii::$app->formatter->asDecimal((float)$avgAll, 1) ?></td>
        <td class="text-right"><?= Yii::$app->formatter->asDecimal((float)$avgNormal, 1) ?></td>
        <td class="text-right"><?= Yii::$app->formatter->asDecimal((float)$avgMistake, 1) ?></td>
      </tr>
      <tr>
        <th class="text-right" scope="row"><?= Html::encode(Yii::t('app-festpower2', 'Q1/4')) ?></th>
        <td class="text-right"><?= Yii::$app->formatter->asInteger((int)$q1All) ?></td>
        <td class="text-right"><?= Yii::$app->formatter->asInteger((int)$q1Normal) ?></td>
        <td class="text-right"><?= Yii::$app->formatter->asInteger((int)$q1Mistake) ?></td>
      </tr>
      <tr>
        <th class="text-right" scope="row"><?= Html::encode(Yii::t('app-festpower2', 'Median')) ?></th>
        <td class="text-right"><?= Yii::$app->formatter->asInteger((int)$medianAll) ?></td>
        <td class="text-right"><?= Yii::$app->formatter->asInteger((int)$medianNormal) ?></td>
        <td class="text-right"><?= Yii::$app->formatter->asInteger((int)$medianMistake) ?></td>
      </tr>
      <tr>
        <th class="text-right" scope="row"><?= Html::encode(Yii::t('app-festpower2', 'Q3/4')) ?></th>
        <td class="text-right"><?= Yii::$app->formatter->asInteger((int)$q3All) ?></td>
        <td class="text-right"><?= Yii::$app->formatter->asInteger((int)$q3Normal) ?></td>
        <td class="text-right"><?= Yii::$app->formatter->asInteger((int)$q3Mistake) ?></td>
      </tr>
      <tr>
        <th class="text-right" scope="row"><?= Html::encode(Yii::t('app-festpower2', 'Std. Dev.')) ?></th>
        <td class="text-right"><?= Yii::$app->formatter->asDecimal((float)$stddevAll, 3) ?></td>
        <td class="text-right"><?= Yii::$app->formatter->asDecimal((float)$stddevNormal, 3) ?></td>
        <td class="text-right"><?= Yii::$app->formatter->asDecimal((float)$stddevMistake, 3) ?></td>
      </tr>
    </tbody>
  </table>
  <div id="winpct" class="graph_ w-100"></div>
<?php $this->registerJs(vsprintf('$(%s).festpowerDiffWinPct(%s);', [
  Json::encode('#winpct'),
  implode(', ', [
    Json::encode($chartData),
    Json::encode([
      'normal_battles' => implode(' ', [
        (string)FA::fas('angle-right')->fw(),
        Html::encode(Yii::t('app-festpower2', 'Battles (normal)')),
      ]),
      'normal_pct' => implode(' ', [
        (string)FA::fas('angle-left')->fw(),
        Html::encode(Yii::t('app-festpower2', 'Greater Win % (normal)')),
      ]),
    ]),
  ]),
])) ?>
  <?= GridView::widget([
    'dataProvider' => Yii::createObject([
      'class' => ArrayDataProvider::class,
      'sort' => false,
      'pagination' => false,
      'allModels' => $data,
    ]),
    'options' => [
      'class' => 'grid-view table-responsive table-responsive-force',
    ],
    'tableOptions' => [
      'class' => 'table table-striped table-hover',
    ],
    'layout' => '{items}',
    'columns' => [
      [
        'label' => Yii::t('app-festpower2', 'Power Diff'),
        'attribute' => 'diff',
        'format' => 'integer',
        'contentOptions' => [
          'class' => 'text-right',
        ],
      ],
      [
        'label' => Yii::t('app-festpower2', 'Battles (all)'),
        'contentOptions' => [
          'class' => 'text-right',
        ],
        'value' => function (array $row) use ($totalBattles): string {
          return vsprintf('%s (%s)', [
            Yii::$app->formatter->asInteger($row['battles']),
            $totalBattles > 0
              ? Yii::$app->formatter->asPercent($row['battles'] / $totalBattles, 2)
              : Yii::t('app-festpower2', 'N/A'),
          ]);
        },
      ],
      [
        'label' => Yii::t('app-festpower2', 'Greater Win % (all)'),
        'contentOptions' => [
          'class' => 'text-right',
        ],
        'value' => function (array $row): ?string {
          return winRateColumn(
            (int)$row['diff'],
            (int)$row['battles'],
            (int)$row['higher_wins']
          );
        },
      ],
      [
        'label' => Yii::t('app-festpower2', 'Battles (normal)'),
        'contentOptions' => [
          'class' => 'text-right',
        ],
        'value' => function (array $row) use ($totalBattles, $totalMistakeBattles): string {
          $total = $totalBattles - $totalMistakeBattles;
          $battles = $row['battles'] - $row['mistake_battles'];
          return vsprintf('%s (%s)', [
            Yii::$app->formatter->asInteger($battles),
            $total > 0
              ? Yii::$app->formatter->asPercent($battles / $total, 2)
              : Yii::t('app-festpower2', 'N/A'),
          ]);
        },
      ],
      [
        'label' => Yii::t('app-festpower2', 'Greater Win % (normal)'),
        'contentOptions' => [
          'class' => 'text-right',
        ],
        'value' => function (array $row): ?string {
          return winRateColumn(
            (int)$row['diff'],
            $row['battles'] - $row['mistake_battles'],
            $row['higher_wins'] - $row['mistake_higher_wins']
          );
        },
      ],
      [
        'label' => Yii::t('app-festpower2', 'Battles (mistaken)'),
        'contentOptions' => [
          'class' => 'text-right',
        ],
        'value' => function (array $row) use ($totalMistakeBattles): string {
          return vsprintf('%s (%s)', [
            Yii::$app->formatter->asInteger($row['mistake_battles']),
            $totalMistakeBattles > 0
              ? Yii::$app->formatter->asPercent($row['mistake_battles'] / $totalMistakeBattles, 2)
              : Yii::t('app-festpower2', 'N/A'),
          ]);
        },
      ],
      [
        'label' => Yii::t('app-festpower2', 'Greater Win % (mistaken)'),
        'contentOptions' => [
          'class' => 'text-right',
        ],
        'value' => function (array $row): ?string {
          return winRateColumn(
            (int)$row['diff'],
            $row['mistake_battles'],
            $row['mistake_higher_wins']
          );
        },
      ],
    ],
  ]) . "\n" ?>
</div>
