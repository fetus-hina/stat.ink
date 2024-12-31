<?php

/**
 * @copyright Copyright (C) 2022-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\models\Salmon3;
use app\models\User;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var Salmon3 $model
 * @var User $user
 * @var View $this
 */

echo Html::a(
  Html::tag(
    'div',
    implode('', [
      Html::tag(
        'div',
        implode('', [
          $this->render('item/result', ['model' => $model]),
          $this->render('item/data', ['model' => $model]),
        ]),
        ['class' => 'simple-battle-row-impl-main'],
      ),
      $this->render('item/time', ['model' => $model]),
      $this->render('item/disconnect', ['model' => $model]),
    ]),
    ['class' => 'simple-battle-row-impl'],
  ),
  ['salmon-v3/view',
    'screen_name' => $user->screen_name,
    'battle' => $model->uuid,
  ],
);
