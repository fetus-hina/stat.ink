<?php

/**
 * @copyright Copyright (C) 2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\widgets\Icon;
use app\models\SalmonWaterLevel2;
use app\models\StatSalmon3MapKingTide;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var View $this
 * @var array<int, SalmonWaterLevel2> $tides
 * @var array<int, StatSalmon3MapKingTide> $tideModels
 */

?>
<?= Html::tag(
  'div',
  implode(
    '',
    array_map(
      fn (SalmonWaterLevel2 $model): string => Html::tag(
        'div',
        implode('', [
          Html::tag('div', Icon::s3SalmonTide($model)),
          $this->render('./pie', [
            'cleared' => $tideModels[$model->id]?->cleared ?? 0,
            'jobs' => $tideModels[$model->id]?->jobs ?? 0,
            'labelText' => false,
          ]),
          Html::tag(
            'div',
            $this->render('./n', ['n' => $tideModels[$model->id]?->jobs ?? 0]),
            ['class' => 'small'],
          ),
        ]),
        ['class' => 'text-center'],
      ),
      $tides,
    ),
  ),
  [
    'class' => 'mt-1',
    'style' => [
      'column-gap' => '10px',
      'display' => 'grid',
      'grid-template-columns' => 'repeat(' . count($tides) . ', 1fr)',
    ],
  ],
) . "\n" ?>
