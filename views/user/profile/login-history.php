<?php

/**
 * @copyright Copyright (C) 2023-2025 AIZAWA Hina
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

echo Html::tag(
  'h2',
  Html::encode(Yii::t('app', 'Login History')),
) . "\n";

echo Html::tag(
  'p',
  implode('', [
    Html::a(
      implode(' ', [
        Icon::loginHistory(),
        Html::encode(Yii::t('app', 'Login History')),
      ]),
      ['user/login-history'],
      ['class' => 'btn btn-default btn-block text-left'],
    ),
  ]),
) . "\n";
