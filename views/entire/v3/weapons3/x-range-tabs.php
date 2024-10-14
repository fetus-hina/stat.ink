<?php

declare(strict_types=1);

use app\components\widgets\Icon;
use app\models\StatWeapon3XUsageRange;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var StatWeapon3XUsageRange $xRange
 * @var StatWeapon3XUsageRange[] $xRanges
 * @var View $this
 * @var bool|null $disableAll
 * @var callable(StatWeapon3XUsageRange|null): string $xRangeUrl
 */

if (!$xRanges) {
  return;
}

$disableAll = $disableAll ?? false;

echo Html::tag(
  'nav',
  Html::tag(
    'ul',
    implode('', [
      $disableAll
        ? ''
        : $this->render('x-range-tabs/all', [
          'isActive' => $xRange === null,
          'xRangeUrl' => $xRangeUrl,
          'xRanges' => $xRanges,
        ]),
      $this->render('x-range-tabs/ranges', [
        'xRange' => $xRange,
        'xRangeUrl' => $xRangeUrl,
        'xRanges' => $xRanges,
      ]),
    ]),
    ['class' => 'nav nav-pills'],
  ),
  ['class' => 'mb-1'],
);
