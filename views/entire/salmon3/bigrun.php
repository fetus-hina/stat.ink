<?php

/**
 * @copyright Copyright (C) 2023-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use MathPHP\Probability\Distribution\Continuous\Normal as NormalDistribution;
use app\assets\NotoSansMathAsset;
use app\components\helpers\OgpHelper;
use app\components\helpers\TypeHelper;
use app\components\widgets\AdWidget;
use app\components\widgets\Icon;
use app\components\widgets\SnsWidget;
use app\models\SalmonSchedule3;
use app\models\StatBigrunDistribJobAbstract3;
use app\models\StatBigrunDistribUserAbstract3;
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
 * @var StatBigrunDistribJobAbstract3|null $jobAbstract
 * @var StatBigrunDistribUserAbstract3|null $abstract
 * @var View $this
 * @var array<int, SalmonSchedule3> $schedules
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

<?php if ($schedules) { ?>
  <div class="mb-3">
    <?= Html::tag(
      'select',
      implode('', array_map(
        fn (SalmonSchedule3 $model): string => Html::tag(
          'option',
          Html::encode(
            vsprintf('%s (%s)', [
              Yii::t(
                'app-map3',
                match (true) {
                  $model->is_random_map_big_run	=> 'Multiple Sites',
                  $model->bigMap !== null => $model->bigMap->name,
                  default=> '?',
                },
              ),
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
          Yii::t(
            'app-map3',
            match (true) {
              $schedule->is_random_map_big_run => 'Multiple Sites',
              $schedule->bigMap !== null => $schedule->bigMap->name,
              default=> '?',
            },
          ),
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
      'border' => $schedule->bigrunOfficialBorder3,
      'model' => $abstract,
      'official' => $schedule->bigrunOfficialResult3,
      'ruleOfThumbDistrib' => $ruleOfThumbDistrib,
    ]) . "\n" ?>

    <div class="row">
      <div class="col-12 col-md-6 mb-3">
        <?= $this->render('bigrun/user-histogram', compact(
          'abstract',
          'chartMax',
          'estimatedDistrib',
          'histogram',
          'normalDistrib',
          'ruleOfThumbDistrib',
        )) . "\n" ?>
      </div>
      <div class="col-12 col-md-6 mb-3">
        <?= $this->render('bigrun/job-histogram', [
          'abstract' => $jobAbstract,
          'histogram' => $jobHistogram,
          'chartMax' => $chartMax,
        ]) . "\n" ?>
      </div>
    </div>
    <div class="row">
      <div class="col-12 col-md-6 mb-3">
        <?= $this->render('bigrun/user-histogram-cdf', compact(
          'abstract',
          'chartMax',
          'estimatedDistrib',
          'histogram',
          'normalDistrib',
          'ruleOfThumbDistrib',
        )) . "\n" ?>
      </div>
    </div>
  </div>
</div>
