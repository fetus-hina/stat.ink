<?php

declare(strict_types=1);

use app\assets\SalmonBadgeAsset;
use yii\bootstrap\Progress;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\AssetManager;
use yii\web\View;

/**
 * @var View $this
 */

$am = Yii::$app->assetManager;
assert($am instanceof AssetManager);

$getStepInfo = function (array $row): array {
  // return [ currentIcon, nextIcon, startCount, nextCount]
  $defeated = (int)ArrayHelper::getValue($row, 'defeated');
  switch (ArrayHelper::getValue($row, 'type')) {
    case 'boss':
      return match (true) {
        $defeated < 100 => [0, 1, 0, 100],
        $defeated < 1000 => [1, 2, 100, 1000],
        $defeated < 10000 => [2, 3, 1000, 10000],
        default => [3, 3, 10000, null],
      };

    case 'king':
      return match (true) {
        $defeated < 10 => [0, 1, 0, 10],
        $defeated < 100 => [1, 2, 10, 100],
        $defeated < 1000 => [2, 3, 100, 1000],
        default => [3, 3, 1000, null],
      };

    default:
      throw new LogicException();
  }
};

$renderStepIcon = fn (string $key, int $step): string => Html::img(
  $am->getAssetUrl(
    $am->getBundle(SalmonBadgeAsset::class),
    sprintf('bosses/%s/%d.png', rawurlencode($key), $step),
  ),
  [
    'class' => 'basic-icon',
    'style' => '--icon-height:2em',
  ],
);

$headerOptions = [
  'class' => 'text-center omit',
];

$value = function (array $row) use ($getStepInfo, $renderStepIcon) {
  [$currentIconStep, $nextIconStep, $startCount, $nextCount] = $getStepInfo($row);
  $current = (int)ArrayHelper::getValue($row, 'defeated');
  $remains = $nextCount === null ? null : ($nextCount - $current);

  return Html::tag(
    'div',
    implode('', [
      $renderStepIcon((string)ArrayHelper::getValue($row, 'key'), $currentIconStep),
      Html::tag(
        'div',
        Progress::widget([
          'bars' => [
            [
              'label' => $nextCount === null
                ? mb_chr(0x1f389) // tada
                : Yii::$app->formatter->asPercent($current / $nextCount, 1),
              // 'options' => ['class' => 'progress-bar-success'],
              'percent' => $nextCount === null
                ? 100 // completed
                : 100 * ($current - $startCount) / ($nextCount - $startCount),
            ],
            [
              'label' => $nextCount === null
                ? ''
                : Yii::t('app', '{nFormatted} remaining', [
                  'n' => $remains,
                  'nFormatted' => Yii::$app->formatter->asInteger($remains),
                ]),
              'options' => ['class' => 'progress-bar-warning'],
              'percent' => $nextCount === null ? 0 : 100 * $remains / ($nextCount - $startCount),
            ],
          ],
        ]),
        ['class' => 'flex-fill px-1'],
      ),
      $renderStepIcon((string)ArrayHelper::getValue($row, 'key'), $nextIconStep),
    ]),
    ['class' => 'align-items-center d-flex w-100'],
  );
};

return [
  'format' => 'raw',
  'headerOptions' => $headerOptions,
  'label' => Yii::t('app', 'Progress'),
  'value' => $value,
];
