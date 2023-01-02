<?php

declare(strict_types=1);

use app\components\widgets\Icon;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var View $this
 */

echo Html::a(
  implode(' ', [
    Icon::fileCsv(),
    Html::encode(Yii::t('app', 'CSV (IkaLog compat.)')),
  ]),
  ['download2', 'type' => 'ikalog-csv'],
  ['class' => 'btn btn-default btn-block text-left']
);
