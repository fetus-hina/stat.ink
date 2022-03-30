<?php

declare(strict_types=1);

use app\components\widgets\SalmonWaves;
use app\models\Salmon2;
use app\models\SalmonWave2;
use yii\web\View;

/**
 * @var Salmon2 $model
 * @var View $this
 */

$waves = SalmonWave2::find()
  ->with(['event', 'water'])
  ->andWhere(['salmon_id' => $model->id])
  ->orderBy(['salmon_wave2.wave' => SORT_ASC])
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
