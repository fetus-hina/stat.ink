<?php

declare(strict_types=1);

use app\components\widgets\FA;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var View $this
 */

?>
<?= Html::a(
  implode(' ', [
    (string)FA::fas('cogs')->fw(),
    Html::encode(Yii::t('app', 'View Settings')),
  ]),
  '#table-config',
  [
    'class' => [
      'btn',
      'btn-default',
    ],
  ],
) ?>
