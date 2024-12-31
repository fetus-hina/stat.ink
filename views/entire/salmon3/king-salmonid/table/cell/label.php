<?php

/**
 * @copyright Copyright (C) 2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\widgets\Icon;
use app\models\BigrunMap3;
use app\models\SalmonKing3;
use app\models\SalmonMap3;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var BigrunMap3|SalmonMap3 $map
 * @var SalmonKing3 $king
 * @var View $this
 */

echo Html::tag(
  'div',
  implode('', [
    match ($map::class) {
      BigrunMap3::class => Icon::s3BigRun(
        alt: implode(' - ', [
          Yii::t('app-salmon3', 'Big Run'),
          Yii::t('app-map3', $map->name),
        ]),
      ),
      SalmonMap3::class => Icon::s3SalmonStage($map),
    },
    Icon::s3BossSalmonid($king),
  ]),
  [
    'style' => [
      'background-color' => 'rgba(255 255 255 / 0.8)',
      'border-bottom-right-radius' => '4px',
      'font-size' => '24px',
      'left' => '0',
      'line-height' => '1',
      'padding' => '4px',
      'position' => 'absolute',
      'top' => '0',
    ],
  ],
);
