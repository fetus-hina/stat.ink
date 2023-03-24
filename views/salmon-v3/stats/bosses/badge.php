<?php

declare(strict_types=1);

use app\models\SalmonBoss3;
use app\models\User;
use yii\data\ArrayDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var View $this
 * @var array{type: string, key: string, name: string, defeated: int}[] $badges
 */

$dataProvider = Yii::createObject([
  'class' => ArrayDataProvider::class,
  'allModels' => $badges,
  'pagination' => false,
  'sort' => false,
]);

?>
<h2 class="mb-3">
  <?= Html::encode(Yii::t('app', 'Badges')) . "\n" ?>
</h2>
<div class="mb-3">
  <?= GridView::widget([
    'dataProvider' => $dataProvider,
    'layout' => '{items}',
    'tableOptions' => [
      'class' => 'mb-0 table table-bordered table-condensed table-striped',
    ],
    'columns' => [
        require __DIR__ . '/badge/columns/boss-salmonid.php',
        require __DIR__ . '/badge/columns/defeated.php',
        require __DIR__ . '/badge/columns/progress.php',
      ],
  ]) . "\n" ?>
</div>
<p class="mb-3 text-muted small">
  <?= Html::encode(Yii::t('app', 'If there are any unsubmitted data, they have not been included in this tally.')) . "\n" ?>
</p>
