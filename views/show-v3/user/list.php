<?php

/**
 * @copyright Copyright (C) 2022-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use yii\data\BaseDataProvider;
use yii\grid\GridView;
use yii\web\View;

/**
 * @var BaseDataProvider $battleDataProvider
 * @var View $this
 */

$columns = array_map(
  function (array $config): array {
    unset($config['-label']);
    return $config;
  },
  require __DIR__ . '/list-options/columns.php',
);

echo GridView::widget([
  'beforeRow' => require __DIR__  . '/list-options/before-row.php',
  'columns' => $columns,
  'dataProvider' => $battleDataProvider,
  'layout' => '{items}',
  'options' => require __DIR__ . '/list-options/options.php',
  'rowOptions' => require __DIR__ . '/list-options/row-options.php',
  'tableOptions' => require __DIR__ . '/list-options/table-options.php',
]);
