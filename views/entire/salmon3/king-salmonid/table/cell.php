<?php

/**
 * @copyright Copyright (C) 2024-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\models\BigrunMap3;
use app\models\SalmonKing3;
use app\models\SalmonMap3;
use app\models\SalmonWaterLevel2;
use app\models\StatSalmon3MapKingTide;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var BigrunMap3|SalmonMap3 $map
 * @var SalmonKing3 $king
 * @var View $this
 * @var array<int, SalmonWaterLevel2> $tides
 * @var array<int, StatSalmon3MapKingTide> $tideModels
 * @var int $cleared
 * @var int $jobs
 */

echo Html::tag(
  'td',
  implode('', [
    $this->render('./cell/pie', [
      'cleared' => $cleared,
      'jobs' => $jobs,
      'labelText' => true,
    ]),
    $this->render('./cell/n', ['n' => $jobs]),
    $this->render('./cell/label', compact('map', 'king')),
    $this->render('./cell/tides', compact('tides', 'tideModels')),
  ]),
  [
    'class' => [
      'align-middle',
      'cell',
      'text-center',
    ],
    'style' => [
      'position' => 'relative',
    ],
  ],
);
