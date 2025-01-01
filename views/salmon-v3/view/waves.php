<?php

/**
 * @copyright Copyright (C) 2022-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\widgets\v3\SalmonWaves;
use app\models\Salmon3;
use app\models\SalmonWave3;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var Salmon3 $model
 * @var SalmonWave3[] $waves
 * @var View $this
 */

if (!$waves) {
  return;
}

$get = fn (array $list, int $wave): ?SalmonWave3 => array_reduce(
  $list,
  fn (?SalmonWave3 $carry, SalmonWave3 $item): ?SalmonWave3 => ($item->wave === $wave)
    ? $item
    : $carry,
  null,
);

?>
<?= Html::tag('h2', Html::encode(Yii::t('app-salmon2', 'Waves')), ['id' => 'waves']) . "\n" ?>
<?= SalmonWaves::widget(array_merge(
  [
    'job' => $model,
    'wave1' => $get($waves, 1),
    'wave2' => $get($waves, 2),
    'wave3' => $get($waves, 3),
  ],
  $model->is_eggstra_work
    ? [
      'wave4' => $get($waves, 4),
      'wave5' => $get($waves, 5),
    ]
    : [
      'extra' => $get($waves, 4),
    ],
)) . "\n" ?>
