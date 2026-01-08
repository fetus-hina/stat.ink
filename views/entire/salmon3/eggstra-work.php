<?php

/**
 * @copyright Copyright (C) 2023-2026 AIZAWA Hina
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
use app\models\SalmonEggstraDistribAbstract3;
use app\models\SalmonSchedule3;
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
 * @var array<int, SalmonEggstraDistribAbstract3>|null $abstract
 * @var array<int, int> $histogram
 * @var int|null $chartMax
 */

$title = Yii::t('app-salmon3', 'Eggstra Work');
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
        <li role="presentation">
          <?= Html::a(
            Icon::s3BigRun() . ' ' . Html::encode(Yii::t('app-salmon3', 'Big Run')),
            ['entire/salmon3-bigrun'],
          ) . "\n" ?>
        </li>
        <li role="presentation" class="active">
          <a>
            <?= Icon::s3Eggstra() . "\n" ?>
            <?= Html::encode(Yii::t('app-salmon3', 'Eggstra Work')) . "\n" ?>
          </a>
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
              Yii::t('app-map3', $model->map?->name ?? '?'),
              $fmt->asDate($model->start_at, 'medium'),
            ]),
          ),
          [
            'value' => Url::to(['entire/salmon3-eggstra-work', 'shift' => $model->id]),
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
          Yii::t('app-map3', $schedule->map?->name ?? '?'),
          $fmt->asDate($schedule->start_at, 'medium'),
        ]),
      ),
      ['class' => 'mt-0 mb-3'],
    ) . "\n" ?>
    <?= $this->render('bigrun/abstract', [
      'border' => null,
      'model' => $abstract,
      'official' => $schedule->eggstraWorkOfficialResult3,
      'ruleOfThumbDistrib' => $ruleOfThumbDistrib,
    ]) . "\n" ?>
    <div class="row">
      <div class="col-xs-12 col-md-9 col-lg-7 mb-3">
        <?= $this->render('bigrun/histogram', compact(
          'abstract',
          'chartMax',
          'estimatedDistrib',
          'histogram',
          'normalDistrib',
          'ruleOfThumbDistrib',
        )) . "\n" ?>
      </div>
    </div>
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
