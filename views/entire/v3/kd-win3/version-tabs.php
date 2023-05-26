<?php

declare(strict_types=1);

use yii\helpers\Html;
use yii\web\View;

/**
 * @var View $this
 */

?>
<aside class="mb-3">
  <nav><?= Html::tag(
    'ul',
    implode('', [
      Html::tag(
        'li',
        Html::tag(
          'a',
          implode(' ', [
            Html::encode(Yii::t('app', 'Splatoon 3')),
          ]),
        ),
        ['class' => 'active'],
      ),
      Html::tag(
        'li',
        Html::a(
          implode(' ', [
            Html::encode(Yii::t('app', 'Splatoon 2')),
          ]),
          ['entire/kd-win2'],
        ),
      ),
      Html::tag(
        'li',
        Html::a(
          implode(' ', [
            Html::encode(Yii::t('app', 'Splatoon')),
          ]),
          ['entire/kd-win'],
        ),
      ),
    ]),
    ['class' => 'nav nav-tabs'],
  ) ?></nav>
</aside>
