<?php

declare(strict_types=1);

use app\components\widgets\Icon;
use yii\helpers\Html;

/**
 * @var View $this
 */

?>
<?= Html::a(
  implode(' ', [
    Icon::search(),
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
