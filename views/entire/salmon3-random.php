<?php

declare(strict_types=1);

use app\components\widgets\AdWidget;
use app\components\widgets\SnsWidget;
use app\components\widgets\v3\WeaponName;
use app\models\SalmonWeapon3;
use yii\bootstrap\Progress;
use yii\data\ArrayDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var View $this
 * @var array<string, SalmonWeapon3> $weapons
 * @var array<string, int> $counts
 * @var int $max
 * @var int $total
 */

$this->context->layout = 'main';

$title  = 'Salmon Run Random Weapon';
$this->title = vsprintf('%s - %s', [
    $title,
    Yii::$app->name,
]);

?>
<div class="container">
  <h1><?= Html::encode($title) ?></h1>

  <?= AdWidget::widget() . "\n" ?>
  <?= SnsWidget::widget() . "\n" ?>

  <div class="table-responsive">
    <table class="table table-striped">
      <thead>
        <tr>
          <th class="text-center" style="width:15em">Weapon</th>
          <th class="text-center" style="width:5em">Count</th>
          <th class="text-center" style="width:5em">%</th>
          <th class="text-center" style="min-width:200px"></th>
        </tr>
      </thead>
      <tbody>
<?php foreach ($counts as $key => $count) { ?>
        <tr>
          <th>
            <?= WeaponName::widget([
              'model' => $weapons[$key] ?? null,
              'showName' => true,
              'subInfo' => false,
            ]) . "\n" ?>
          </th>
          <td class="text-right">
            <?= Yii::$app->formatter->asInteger($count) . "\n" ?>
          </td>
          <td class="text-right">
            <?= Yii::$app->formatter->asPercent($count / $total, 2) . "\n" ?>
          </td>
          <td class="text-left">
            <?= Progress::widget([
              'percent' => 100 * $count / $max,
              'barOptions' => [
                'class' => 'progress-bar-info',
              ],
              'options' => [
                'class' => ['progress-striped'],
                'style' => 'min-width:200px;max-width:500px',
              ],
            ]) . "\n" ?>
          </td>
        </tr>
<?php } ?>
      </tbody>
    </table>
  </div>
</div>
