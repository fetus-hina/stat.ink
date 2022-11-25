<?php

declare(strict_types=1);

use yii\data\BaseDataProvider;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ListView;

/**
 * @var BaseDataProvider $dataProvider
 * @var View $this
 */

echo Html::tag(
  'div',
  ListView::widget([
    'dataProvider' => $dataProvider,
    'itemOptions' => [
      'tag' => false,
    ],
    'layout' => '{pager}',
    'pager' => [
      'maxButtonCount' => 5
    ],
  ]),
  ['class' => 'text-center'],
);
