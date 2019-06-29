<?php
/**
 * @copyright Copyright (C) 2015-2018 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use yii\data\Pagination;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

Yii::$classMap[ArrayHelper::class] = __DIR__ . '/../components/overwrite/yii/helpers/ArrayHelper.php';
Yii::$classMap[Html::class] = __DIR__ . '/../components/overwrite/yii/helpers/Html.php';

Yii::$container->set(Pagination::class, [
    'defaultPageSize' => 100,
    'pageSizeLimit' => [1, 200],
]);
