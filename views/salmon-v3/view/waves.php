<?php

declare(strict_types=1);

use app\components\widgets\v3\SalmonWaves;
use app\models\Salmon3;
use app\models\SalmonWave3;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var Salmon3 $model
 * @var View $this
 */

$waves = SalmonWave3::find()
  ->with([
    'event',
    'salmonSpecialUse3s',
    'salmonSpecialUse3s.special',
    'tide',
  ])
  ->andWhere(['salmon_id' => $model->id])
  ->orderBy([
    'salmon_id' => SORT_ASC,
    'wave' => SORT_ASC,
  ])
  ->all();
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
<?= SalmonWaves::widget([
  'job' => $model,
  'wave1' => $get($waves, 1),
  'wave2' => $get($waves, 2),
  'wave3' => $get($waves, 3),
  'extra' => $get($waves, 4),
]) . "\n" ?>
