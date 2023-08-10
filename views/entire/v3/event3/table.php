<?php

declare(strict_types=1);

use app\assets\TableResponsiveForceAsset;
use app\components\helpers\TypeHelper;
use app\models\Event3StatsSpecial;
use app\models\Event3StatsWeapon;
use statink\yii2\sortableTable\SortableTableAsset;
use yii\base\InvalidConfigException;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\web\View;

$modelClass = TypeHelper::instanceOf($provider->query, ActiveQuery::class)->modelClass;
if (
  $modelClass !== Event3StatsSpecial::class &&
  $modelClass !== Event3StatsWeapon::class
) {
  throw new LogicException('Unexpected model class');
}

$maxUseRate = $samples > 0
  ? max(
    array_map(
      fn (Event3StatsSpecial|Event3StatsWeapon $model): float => $model->battles / $samples,
      $provider->getModels(),
    ),
  )
  : 0.0;

$maxWinRate = min(
  0.75,
  max(
    array_filter(
      array_map(
        function (Event3StatsSpecial|Event3StatsWeapon $model): ?float {
          $wins = $model->wins;
          $battles = $model->battles;
          return $battles > 0 ? $wins / $battles : null;
        },
        $provider->getModels(),
      ),
      fn (?float $v): bool => $v !== null,
    ),
  ),
);

$columns = match ($modelClass) {
  Event3StatsSpecial::class => require __DIR__ . '/table/columns-special.php',
  Event3StatsWeapon::class => require __DIR__ . '/table/columns-weapon.php',
};

TableResponsiveForceAsset::register($this);
SortableTableAsset::register($this);

?>
<div class="mb-3">
  <?= GridView::widget([
    'columns' => $columns,
    'dataProvider' => $provider,
    'emptyCell' => '',
    'emptyText' => '',
    'layout' => '{items}',
    'options' => ['class' => 'grid-view mb-2 table-responsive table-responsive-force'],
    'tableOptions' => ['class' => 'mb-0 table table-condensed table-hover table-sortable table-striped'],
  ]) . "\n" ?>
  <?= Html::tag(
    'p',
    Html::encode('*: p < 0.05 / **: p < 0.01'),
    ['class' => ['mb-2', 'small', 'text-muted', 'text-right']],
  ) . "\n" ?>
</div>
