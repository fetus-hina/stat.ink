<?php

declare(strict_types=1);

use app\models\User;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var User $user
 * @var View $this
 */

?>
<?= Html::tag(
  'div',
  implode(' ', [
    $this->render('buttons/search'),
    $this->render('buttons/config'),
    $this->render('buttons/simplified-list', ['user' => $user]),
  ]),
  [
    'class' => 'mb-2',
  ],
) ?>
