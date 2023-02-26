<?php

declare(strict_types=1);

use app\assets\Spl3SalmonUniformAsset;
use app\assets\Spl3SalmonidAsset;
use app\components\helpers\TypeHelper;
use app\models\SalmonBoss3;
use app\models\User;
use statink\yii2\sortableTable\SortableTableAsset;
use yii\data\ArrayDataProvider;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\AssetManager;
use yii\web\View;

/**
 * @var User $user
 * @var View $this
 * @var array<string, SalmonBoss3> $bosses
 * @var array<string, array{boss_key: string, appearances: int, defeated: int, defeated_by_me: int}> $stats
 */

SortableTableAsset::register($this);

$am = Yii::$app->assetManager;
assert($am instanceof AssetManager);

$dataProvider = Yii::createObject([
  'class' => ArrayDataProvider::class,

  'allModels' => $stats,
  'pagination' => false,
  'sort' => false,
]);

echo GridView::widget([
  'dataProvider' => $dataProvider,
  'layout' => '{items}',
  'tableOptions' => ['class' => 'mb-0 table table-bordered table-condensed table-sortable table-striped'],
  'columns' => [
    [
      'contentOptions' => function (array $row) use ($bosses): array {
        $key = TypeHelper::string(ArrayHelper::getValue($row, 'boss_key'));
        $boss = ArrayHelper::getValue($bosses, $key);
        if (!$boss instanceof SalmonBoss3) {
          return [];
        }
        return [
          'data' => [
            'sort-value' => Yii::t('app-salmon-boss3', $boss->name),
          ],
        ];
      },
      'format' => 'raw',
      'headerOptions' => [
        'class' => 'text-center',
        'data' => [
          'sort' => 'string',
          'sort-default' => 'asc',
        ],
      ],
      'label' => Yii::t('app-salmon3', 'Boss Salmonid'),
      'value' => function (array $row) use ($am, $bosses): string {
        $key = TypeHelper::string(ArrayHelper::getValue($row, 'boss_key'));
        $boss = ArrayHelper::getValue($bosses, $key);
        if ($boss instanceof SalmonBoss3) {
          return implode(' ', [
            Html::img(
              $am->getAssetUrl(
                $am->getBundle(Spl3SalmonidAsset::class),
                sprintf('%s.png', rawurlencode($key)),
              ),
              [
                'class' => 'basic-icon',
                'draggable' => 'false',
              ],
            ),
            Html::encode(
              Yii::t('app-salmon-boss3', $boss->name),
            ),
          ]);
        }
        return '';
      },
    ],
    [
      'attribute' => 'defeated',
      'contentOptions' => fn (array $row): array => [
        'class' => 'text-right',
        'data-sort-value' => (int)ArrayHelper::getValue($row, 'defeated'),
      ],
      'format' => 'integer',
      'headerOptions' => [
        'class' => 'text-center',
        'data' => [
          'sort' => 'int',
          'sort-default' => 'desc',
          'sort-onload' => 'yes',
        ],
      ],
      'label' => Yii::t('app-salmon3', 'Defeated'),
    ],
    [
      'attribute' => 'defeated_by_me',
      'contentOptions' => fn (array $row): array => [
        'class' => 'text-right',
        'data-sort-value' => (int)ArrayHelper::getValue($row, 'defeated_by_me'),
      ],
      'encodeLabel' => false,
      'format' => 'integer',
      'headerOptions' => [
        'class' => 'auto-tooltip text-center',
        'data' => [
          'sort' => 'int',
          'sort-default' => 'desc',
        ],
        'title' => Yii::t('app-salmon3', 'Defeated by {user}', ['user' => $user->name]),
      ],
      'label' => implode(' ', [
        Html::img(
          $am->getAssetUrl($am->getBundle(Spl3SalmonUniformAsset::class), 'orange.png'),
          [
            'class' => 'basic-icon',
            'draggable' => 'false',
          ],
        ),
        Html::encode($user->name),
      ]),
    ],
    [
      'attribute' => 'appearances',
      'contentOptions' => fn (array $row): array => [
        'class' => 'text-right',
        'data-sort-value' => (int)ArrayHelper::getValue($row, 'appearances'),
      ],
      'format' => 'integer',
      'headerOptions' => [
        'class' => 'text-center',
        'data' => [
          'sort' => 'int',
          'sort-default' => 'desc',
        ],
      ],
      'label' => Yii::t('app-salmon3', 'Appearances'),
    ],
  ],
]);
