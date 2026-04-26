<?php

/**
 * @copyright Copyright (C) 2026 AIZAWA Hina
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
  'h2',
  Html::encode(Yii::t('app-recovery-key', 'Recovery Keys')),
) . "\n";

echo Html::tag(
  'p',
  Html::a(
    Html::encode(Yii::t('app-recovery-key', 'Recovery Keys')),
    ['user/recovery-key'],
    ['class' => 'btn btn-default btn-block text-left'],
  ),
) . "\n";
