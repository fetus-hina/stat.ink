<?php

declare(strict_types=1);

use app\components\widgets\v3\SalmonBosses;
use app\models\Salmon3;
use app\models\SalmonBossAppearance3;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var Salmon3 $model
 * @var View $this
 */

$hasData = SalmonBossAppearance3::find()
  ->andWhere(['salmon_id' => $model->id])
  ->andWhere(['>', 'appearances', 0])
  ->exists();

if (!$hasData) {
  return;
}

?>
<?= Html::tag('h2', Html::encode(Yii::t('app-salmon2', 'Boss Salmonids'))) . "\n" ?>
<?= SalmonBosses::widget([
  'job' => $model,
]) . "\n" ?>
