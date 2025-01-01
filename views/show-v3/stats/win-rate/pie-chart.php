<?php

/**
 * @copyright Copyright (C) 2022-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\assets\RatioAsset;
use app\assets\UserStat3WinRateAsset;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\web\View;

/**
 * @var View $this
 * @var bool $canKnockout
 * @var int $loseKnockout
 * @var int $loseTime
 * @var int $loseUnknown
 * @var int $winKnockout
 * @var int $winTime
 * @var int $winUnknown
 */

RatioAsset::register($this);
UserStat3WinRateAsset::register($this);

$winTotal = $winKnockout + $winTime + $winUnknown;
$loseTotal = $loseKnockout + $loseTime + $loseUnknown;
$battles = $winTotal + $loseTotal;

$pieClass = 'pie-' . substr(hash('sha256', __FILE__), 0, 8);
$this->registerJs(sprintf('jQuery(%s).v3WinRate();', Json::encode(".{$pieClass}")));

echo Html::tag(
  'div',
  Html::tag(
    'div',
    '',
    [
      'class' => $pieClass,
      'data' => [
        'values' => Json::encode(
          compact([
            'battles',
            'canKnockout',
            'loseKnockout',
            'loseTime',
            'loseTotal',
            'loseUnknown',
            'winKnockout',
            'winTime',
            'winTotal',
            'winUnknown',
          ]),
        ),
        'labels' => Json::encode([
          'knockout' => Yii::t('app', 'Knockout'),
          'lose' => Yii::t('app', 'Defeat'),
          'time' => Yii::t('app', 'Time is up'),
          'unknown' => Yii::t('app', 'Unknown'),
          'win' => Yii::t('app', 'Victory'),
        ]),
      ],
    ],
  ),
  ['class' => 'ratio ratio-1x1'],
);
