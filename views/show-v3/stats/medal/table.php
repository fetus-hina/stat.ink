<?php

/**
 * @copyright Copyright (C) 2023-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\models\MedalCanonical3;
use app\models\Rule3;
use app\models\User;
use statink\yii2\sortableTable\SortableTableAsset;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var User $user
 * @var View $this
 * @var array<string, MedalCanonical3> $medals
 * @var array<string, Rule3> $rules
 * @var array<string, array<string, int>> $stats
 */

SortableTableAsset::register($this);

// $this->registerCss(
//   Html::renderCss([
//     '.graph-container' => [
//       'table-layout' => 'auto',
//     ],
//     '.graph-container th' => [
//       'min-width' => '150px',
//       'width' => '15%',
//     ],
//     '.graph-container th:first-child' => [
//       'max-width' => '10%',
//       'min-width' => '10%',
//       'width' => '10%',
//     ],
//   ]),
// );
// $this->registerCss('@media screen and (min-width:768px){.graph-container{table-layout:fixed}}');

echo Html::tag(
  'table',
  implode('', [
    $this->render('table/header', compact('rules', 'user')),
    $this->render('table/body', compact('medals', 'rules', 'stats', 'user')),
  ]),
  [
    'class' => [
      'graph-container',
      'mb-0',
      'table',
      'table-condensed',
      'table-sortable',
      'table-striped',
    ],
  ],
);
