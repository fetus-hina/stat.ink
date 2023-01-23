<?php

declare(strict_types=1);

use app\models\Rule3;
use app\models\User;
use app\models\Weapon3;
use statink\yii2\sortableTable\SortableTableAsset;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var User $user
 * @var View $this
 * @var array<string, Rule3> $rules
 * @var array<string, Weapon3> $weapons
 * @var array<string, array<string, array>> $stats
 */

SortableTableAsset::register($this);

$this->registerCss(
  Html::renderCss([
    '.graph-container' => [
      'table-layout' => 'auto',
    ],
    '.graph-container th' => [
      'min-width' => '150px',
      'width' => '15%',
    ],
    '.graph-container th:first-child' => [
      'max-width' => '10%',
      'min-width' => '10%',
      'width' => '10%',
    ],
  ]),
);
$this->registerCss('@media screen and (min-width:768px){.graph-container{table-layout:fixed}}');

echo Html::tag(
  'table',
  implode('', [
    $this->render('table/header', compact('rules', 'user')),
    $this->render(
      'table/body',
      compact(
        'rules',
        'stats',
        'user',
        'weapons',
      ),
    ),
  ]),
  [
    'class' => [
      'graph-container',
      'table',
      'table-condensed',
      'table-sortable',
    ],
  ],
);
