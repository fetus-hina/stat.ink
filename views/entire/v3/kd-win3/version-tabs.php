<?php

/**
 * @copyright Copyright (C) 2023-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\widgets\Icon;
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
            Icon::splatoon3(),
            Html::encode(Yii::t('app', 'Splatoon 3')),
          ]),
        ),
        ['class' => 'active'],
      ),
      Html::tag(
        'li',
        Html::a(
          implode(' ', [
            Icon::splatoon2(),
            Html::encode(Yii::t('app', 'Splatoon 2')),
          ]),
          ['entire/kd-win2'],
        ),
      ),
      Html::tag(
        'li',
        Html::a(
          implode(' ', [
            Icon::splatoon1(),
            Html::encode(Yii::t('app', 'Splatoon')),
          ]),
          ['entire/kd-win'],
        ),
      ),
    ]),
    ['class' => 'nav nav-tabs'],
  ) ?></nav>
</aside>
