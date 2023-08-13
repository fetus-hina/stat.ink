<?php

declare(strict_types=1);

use app\components\helpers\StandardError;
use app\components\widgets\Icon;
use yii\bootstrap\Progress;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var View $this
 * @var array[] $tricolorStats
 */

$fmt = Yii::$app->formatter;

$wins = 0;
$total = 0;
foreach ($tricolorStats as $row) {
  $total += ArrayHelper::getValue($row, 'count', 0);
  if (ArrayHelper::getValue($row, 'is_attacker_wins')) {
    $wins += ArrayHelper::getValue($row, 'count', 0);
  }
}

$errInfo = StandardError::winpct($wins, $total);

?>
<div class="panel panel-default mb-3">
  <div class="panel-heading">
    <?= Html::encode(Yii::t('app-rule3', 'Tricolor Battle')) . "\n" ?>
  </div>
  <div class="panel-body pb-0">
<?php if ($wins < 10 || $total < 100 || !$errInfo) { ?>
    <p class="text-muted mb-3">
      <?= Html::encode(
        Yii::t('app', 'Not enough data is available.'),
      ) . "\n" ?>
    </p>
<?php } else { ?>
    <p class="mb-1 small text-muted">
      <?= Html::encode(
        vsprintf('%s: %s', [
          Yii::t('app', 'Samples'),
          Yii::$app->formatter->asInteger($total),
        ]),
      ) . "\n" ?>
    </p>
    <p class="mb-3">
      <?= Html::encode(
        vsprintf('%s: %s±%s%% (95%%CI)', [
          Yii::t('app', 'Attacker Team Win Ratio'),
          $fmt->asDecimal($errInfo['rate'] * 100, 1),
          $fmt->asDecimal($errInfo['err95ci'] * 100, 1),
        ]),
      ) . "\n" ?>
    </p>
    <?= Progress::widget([
      'bars' => [
        [
          'percent' => $errInfo['min95ci'] * 100,
          'label' => Yii::t('app', '{from} - {to}', [
            'from' => $fmt->asPercent($errInfo['min95ci'], 1),
            'to' => $fmt->asPercent($errInfo['max95ci'], 1),
          ]),
          'options' => [
            'class' => 'progress-bar-primary',
          ]
        ],
        [
          'percent' => ($errInfo['max95ci'] - $errInfo['min95ci']) * 100,
          'label' => '',
          'options' => [
            'class' => 'progress-bar-info',
          ]
        ],
        [
          'percent' => (1.0 - $errInfo['max95ci']) * 100,
          'label' => '',
          'options' => [
            'class' => 'progress-bar-danger',
          ]
        ],
      ],
      'options' => [
        'class' => 'mb-1',
      ],
    ]) . "\n" ?>
    <?= Progress::widget([
      'bars' => [
        [
          'percent' => 50,
          'label' => Yii::t('app', '50% (reference)'),
          'options' => [
            'class' => 'progress-bar-success',
          ],
        ],
        [
          'percent' => 50,
          'label' => Yii::t('app', '50% (reference)'),
          'options' => [
            'class' => 'progress-bar-danger',
          ],
        ],
      ],
      'options' => [
        'class' => 'mb-3',
        'style' => 'opacity:0.5',
      ],
    ]) . "\n" ?>
<?php } ?>
  </div>
</div>
