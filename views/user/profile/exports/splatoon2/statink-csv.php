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

echo Html::a(
  implode(' ', [
    Icon::fileCsv(),
    Html::encode(Yii::t('app', 'CSV')),
  ]),
  ['download2', 'type' => 'csv'],
  ['class' => 'btn btn-default btn-block text-left'],
);
