<?php

declare(strict_types=1);

use app\components\helpers\OgpHelper;
use app\components\widgets\AdWidget;
use app\components\widgets\SnsWidget;
use app\models\BigrunMap3;
use app\models\SalmonEvent3;
use app\models\SalmonKing3;
use app\models\SalmonMap3;
use app\models\StatSalmon3MapKing;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var View $this
 * @var array<int, BigrunMap3> $bigMaps
 * @var array<int, SalmonKing3> $kings
 * @var array<int, SalmonMap3> $maps
 * @var array<int, StatSalmon3MapKing> $data
 */

$this->context->layout = 'main';

$title = vsprintf('%s - %s', [
  Yii::t('app-salmon3', 'Salmon Run'),
  Yii::t('app-salmon3', 'King Salmonid Defeat Rate'),
]);
$this->title = vsprintf('%s | %s', [
  $title,
  Yii::$app->name,
]);

OgpHelper::default($this, title: $this->title);

?>
<div class="container">
  <h1><?= Html::encode($title) ?></h1>

  <?= AdWidget::widget() . "\n" ?>
  <?= SnsWidget::widget() . "\n" ?>

  <?= $this->render('king-salmonid/table', compact('bigMaps', 'kings', 'maps', 'data')) . "\n" ?>
</div>
