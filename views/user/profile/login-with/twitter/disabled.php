<?php

/**
 * @copyright Copyright (C) 2023-2024 AIZAWA Hina
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

echo implode(' ', [
  Html::encode(Yii::t('app', 'Disabled')),
  Html::a(
    implode(' ', [
      Icon::appLink(),
      Html::encode(Yii::t('app', 'Integrate')),
    ]),
    ['update-login-with-twitter'],
    ['class' => 'btn btn-primary'],
  ),
]);
