<?php

declare(strict_types=1);

use MathPHP\Probability\Distribution\Continuous\Normal as NormalDistribution;
use app\assets\NotoSansMathAsset;
use app\components\helpers\OgpHelper;
use app\components\helpers\TypeHelper;
use app\components\widgets\AdWidget;
use app\components\widgets\Icon;
use app\components\widgets\SnsWidget;
use app\models\SalmonSchedule3;
use app\models\StatBigrunDistribAbstract3;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\AssetManager;
use yii\web\View;

/**
 * @var NormalDistribution|null $estimatedDistrib
 * @var NormalDistribution|null $normalDistrib
 * @var NormalDistribution|null $ruleOfThumbDistrib
 * @var SalmonSchedule3 $schedule
 * @var View $this
 * @var array<int, SalmonSchedule3> $schedules
 * @var array<int, StatBigrunDistribAbstract3>|null $abstract
 * @var array<int, int> $histogram
 * @var int|null $chartMax
 */

$title = Yii::t('app-salmon3', 'Big Run');
$this->title = implode(' | ', [
  $title,
  Yii::$app->name,
]);

OgpHelper::default($this, title: $this->title);

$fmt = Yii::$app->formatter;

$am = TypeHelper::instanceOf(Yii::$app->assetManager, AssetManager::class);

?>
<div class="container">
  <h1><?= Html::encode($title) ?></h1>
  <?= AdWidget::widget() . "\n" ?>
  <?= SnsWidget::widget() . "\n" ?>

  <aside class="mb-3">
    <nav>
      <ul class="nav nav-tabs">
        <li role="presentation" class="active">
          <a>
            <?= Icon::s3BigRun() . "\n" ?>
            <?= Html::encode(Yii::t('app-salmon3', 'Big Run')) . "\n" ?>
          </a>
        </li>
        <li role="presentation">
          <?= Html::a(
            Icon::s3Eggstra() . ' ' . Html::encode(Yii::t('app-salmon3', 'Eggstra Work')),
            ['entire/salmon3-eggstra-work'],
          ) . "\n" ?>
        </li>
      </ul>
    </nav>
  </aside>

  <div class="alert alert-danger mb-3">
    <?= Html::encode(
      Yii::t('app', 'This data is based on {siteName} users and differs significantly from overall game statistics.', [
        'siteName' => Yii::$app->name,
      ]),
    ) . "\n" ?>
  </div>

  <div class="alert alert-info mb-3">
    <?= Html::encode(
      Yii::t('app', 'In the chart, "{representative}" means greater than or equal to {min} and less than {max}.', [
        'representative' => $fmt->asInteger(100),
        'min' => $fmt->asInteger(100),
        'max' => $fmt->asInteger(105),
      ]),
    ) . "\n" ?>
    <?= Html::tag(
      'span',
      '(100 ≤ 𝑛 &lt; 105 → 100)',
      ['style' => ['font-family' => 'Noto Sans Math']],
    ) . "\n" ?>
  </div>

<?php if ($schedules) { ?>
  <div class="mb-3">
    <?= Html::tag(
      'select',
      implode('', array_map(
        fn (SalmonSchedule3 $model): string => Html::tag(
          'option',
          Html::encode(
            vsprintf('%s (%s)', [
              Yii::t('app-map3', $model->bigMap?->name ?? '?'),
              $fmt->asDate($model->start_at, 'medium'),
            ]),
          ),
          [
            'value' => Url::to(['entire/salmon3-bigrun', 'shift' => $model->id]),
            'selected' => $schedule->id === $model->id,
          ],
        ),
        $schedules,
      )),
      [
        'class' => 'form-control m-0',
        'onchange' => 'window.location.href = this.value',
      ],
    ) . "\n" ?>
  </div>
<?php } ?>

  <div class="mb-3">
    <?= Html::tag(
      'h2',
      Html::encode(
        vsprintf('%s (%s)', [
          Yii::t('app-map3', $schedule->bigMap?->name ?? '?'),
          $fmt->asDate($schedule->start_at, 'medium'),
        ]),
      ),
      ['class' => 'mt-0 mb-3'],
    ) . "\n" ?>
    <p class="mb-3">
      <?= Html::a(
        Html::encode(Yii::t('app-salmon3', 'Water Level and Events')),
        ['entire/salmon3-tide'],
      ) . "\n" ?>
    </p>
    <?= $this->render('bigrun/abstract', [
      'model' => $abstract,
      'official' => $schedule->bigrunOfficialResult3,
      'ruleOfThumbDistrib' => null,
    ]) . "\n" ?>
    <?= $this->render('bigrun/histogram', compact(
      'abstract',
      'chartMax',
      'estimatedDistrib',
      'histogram',
      'normalDistrib',
      'ruleOfThumbDistrib',
    )) . "\n" ?>
    <?= $this->render('bigrun/histogram2', compact(
      'abstract',
      'chartMax',
      'estimatedDistrib',
      'histogram',
      'normalDistrib',
      'ruleOfThumbDistrib',
    )) . "\n" ?>
  </div>
</div>
