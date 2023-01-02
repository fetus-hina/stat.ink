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
    Icon::fileJson(),
    Html::encode(Yii::t('app', 'JSON (IkaLog compat.)')),
  ]),
  ['download', 'type' => 'ikalog-json'],
  ['class' => 'btn btn-default btn-block text-left'],
);
