<?php

/**
 * @copyright Copyright (C) 2022-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\widgets\v3\SalmonIndexRowHeader;
use app\models\Salmon3;
use yii\grid\GridView;
use yii\web\View;

/**
 * @var View $this
 */

return fn (Salmon3 $model, int $key, int $index, GridView $widget): ?string => SalmonIndexRowHeader::widget([
  'model' => $model,
  'gridView' => $widget,
]);
