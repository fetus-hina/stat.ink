<?php

/**
 * @copyright Copyright (C) 2023-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use yii\helpers\Html;
use yii\web\View;

/**
 * @var View $this
 */

echo Html::tag(
  'div',
  Html::tag(
    'p',
    Html::encode(
      Yii::t(
        'app',
        'This data is based on {siteName} users and differs significantly from overall game statistics.',
        [
          'siteName' => Yii::$app->name,
        ],
      ),
    ),
    ['class' => 'small my-0'],
  ),
  ['class' => 'alert alert-danger mb-3'],
) . "\n";

echo Html::tag(
  'div',
  Html::tag(
    'p',
    Html::encode(
      Yii::t(
        'app',
        'The width of the histogram bins is automatically adjusted by Scott\'s rule-based algorithm.',
      ),
    ),
    ['class' => 'small my-0'],
  ),
  ['class' => 'alert alert-info mb-3'],
) . "\n";
