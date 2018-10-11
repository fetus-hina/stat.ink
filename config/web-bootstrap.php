<?php
/**
 * @copyright Copyright (C) 2015-2018 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

declare(strict_types=1);

use yii\data\Pagination;
use yii\helpers\Html;

Yii::$classMap[Html::class] = __DIR__ . '/../components/overwrite/yii/helpers/Html.php';

Yii::$container->set(Pagination::class, [
    'defaultPageSize' => 100,
    'pageSizeLimit' => [1, 200],
]);
