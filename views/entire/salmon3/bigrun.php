<?php

declare(strict_types=1);

use app\assets\GameModeIconsAsset;
use app\assets\NotoSansMathAsset;
use app\components\widgets\AdWidget;
use app\components\widgets\SnsWidget;
use app\models\SalmonSchedule3;
use app\models\StatBigrunDistribAbstract3;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;

/**
 * @var SalmonSchedule3 $schedule
 * @var View $this
 * @var array<int, SalmonSchedule3> $schedules
 * @var array<int, StatBigrunDistribAbstract3>|null $abstract
 * @var array<int, float>|null $estimatedDistrib
 * @var array<int, float>|null $normalDistrib
 * @var array<int, int> $histogram
 */

$title = Yii::t('app-salmon3', 'Big Run');
$this->title = Yii::$app->name . ' | ' . $title;

$this->registerMetaTag(['name' => 'twitter:card', 'content' => 'summary']);
$this->registerMetaTag(['name' => 'twitter:title', 'content' => $title]);
$this->registerMetaTag(['name' => 'twitter:description', 'content' => $title]);
$this->registerMetaTag(['name' => 'twitter:site', 'content' => '@stat_ink']);

$expires = 1;
$fmt = Yii::$app->formatter;

?>
<div class="container">
  <h1>
    <?= implode(' ', [
      Html::img(
        Yii::$app->assetManager->getAssetUrl(
          Yii::$app->assetManager->getBundle(GameModeIconsAsset::class),
          'spl3/salmon-bigrun.png',
        ),
        [
          'class' => 'basic-icon',
          'draggable' => 'false',
          'style' => ['--icon-height' => '1em'],
        ],
      ),
      Html::encode($title),
    ]) . "\n" ?>
  </h1>
  <?= AdWidget::widget() . "\n" ?>
  <?= SnsWidget::widget() . "\n" ?>

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
      '(100 ≤ 𝑛 ≤ 104 → 100)',
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
              $fmt->asDate($model->start_at, 'short'),
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
      implode(' ', [
        Html::img(
          Yii::$app->assetManager->getAssetUrl(
            Yii::$app->assetManager->getBundle(GameModeIconsAsset::class),
            'spl3/salmon-bigrun.png',
          ),
          [
            'class' => 'basic-icon',
            'draggable' => 'false',
            'style' => ['--icon-height' => '1em'],
          ],
        ),
        Html::encode(
          vsprintf('%s (%s)', [
            Yii::t('app-map3', $schedule->bigMap?->name ?? '?'),
            $fmt->asDate($schedule->start_at, 'short'),
          ]),
        ),
      ]),
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
    ]) . "\n" ?>
    <?= $this->render('bigrun/histogram', [
      'abstract' => $abstract,
      'estimatedDistrib' => $estimatedDistrib,
      'histogram' => $histogram,
      'normalDistrib' => $normalDistrib,
    ]) . "\n" ?>
  </div>
</div>
