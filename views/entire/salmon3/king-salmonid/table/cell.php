<?php

declare(strict_types=1);

use app\models\BigrunMap3;
use app\models\SalmonKing3;
use app\models\SalmonMap3;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var BigrunMap3|SalmonMap3 $map
 * @var SalmonKing3 $king
 * @var View $this
 * @var int $cleared
 * @var int $jobs
 */

echo Html::tag(
  'td',
  implode('', [
    $this->render('./cell/pie', compact('cleared', 'jobs')),
    $this->render('./cell/n', ['n' => $jobs]),
    $this->render('./cell/label', compact('map', 'king')),
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
