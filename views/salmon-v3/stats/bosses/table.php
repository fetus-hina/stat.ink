<?php

declare(strict_types=1);

use app\models\SalmonBoss3;
use app\models\User;
use yii\data\ArrayDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var User $user
 * @var View $this
 * @var array<string, SalmonBoss3> $bosses
 * @var array<string, array{boss_key: string, appearances: int, defeated: int, defeated_by_me: int}> $stats
 */

$dataProvider = Yii::createObject([
  'class' => ArrayDataProvider::class,

  'allModels' => $stats,
  'pagination' => false,
  'sort' => false,
]);

echo Html::tag(
  'div',
  Html::tag(
    'div',
    GridView::widget([
      'dataProvider' => $dataProvider,
      'layout' => '{items}',
      'tableOptions' => [
        'class' => 'mb-0 table table-bordered table-condensed table-sortable table-striped',
      ],
      'columns' => [
        require __DIR__ . '/table/columns/boss-salmonid.php',
        require __DIR__ . '/table/columns/defeated.php',
        require __DIR__ . '/table/columns/defeated-by-me.php',
        require __DIR__ . '/table/columns/appearances.php',
        require __DIR__ . '/table/columns/defeat-rate.php',
        require __DIR__ . '/table/columns/my-defeat-rate.php',
        require __DIR__ . '/table/columns/contribution.php',
      ],
    ]),
    ['class' => 'table-responsive'],
  ),
  ['class' => 'mb-3'],
);
