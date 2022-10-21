<?php

declare(strict_types=1);

use app\components\widgets\FA;
use yii\helpers\Html;

/**
 * @var View $this
 */

?>
<?= Html::a(
  implode(' ', [
    (string)FA::fas('search')->fw(),
    Html::encode(Yii::t('app', 'Search')),
  ]),
  '#filter-form',
  [
    'class' => [
      'visible-xs-inline-block',
      'btn',
      'btn-info',
    ],
  ],
) ?>
