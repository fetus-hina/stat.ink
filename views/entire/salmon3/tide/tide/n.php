<?php

/**
 * @copyright Copyright (C) 2022-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use yii\helpers\Html;
use yii\web\View;

/**
 * @var View $this
 * @var int $n
 */

echo Html::tag(
  'p',
  Html::encode(
    \vsprintf('n = %s', [
      Yii::$app->formatter->asInteger($n),
    ]),
  ),
  [
    'class' => [
      'mb-2',
      'mt-0',
      'small',
      'text-center',
      'text-muted',
    ],
  ],
);
