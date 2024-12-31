<?php

/**
 * @copyright Copyright (C) 2024 AIZAWA Hina
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
  'div',
  vsprintf('%s = %s', [
    Html::tag('span', 'n', ['class' => 'font-italic']),
    Yii::$app->formatter->asInteger($n),
  ]),
  [
    'class' => [
      'mt-1',
      'small',
      'text-center',
      'text-muted',
    ],
  ],
);
