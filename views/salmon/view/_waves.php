<?php
declare(strict_types=1);

use app\components\widgets\SalmonWaves;

$waves = $model->getWaves()
    ->with(['event', 'water'])
    ->all();
if (!$waves) {
    return '';
}
?>
<h2><?= Yii::t('app-salmon2', 'Waves') ?></h2>
<?= SalmonWaves::widget([
  'work' => $model,
  'wave1' => $waves[0] ?? null,
  'wave2' => $waves[1] ?? null,
  'wave3' => $waves[2] ?? null,
]) ?>
