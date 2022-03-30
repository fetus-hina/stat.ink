<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use yii\base\Widget;
use yii\data\Pagination;
use yii\widgets\Pjax;

Widget::$autoIdPrefix = sprintf('w-%s-', substr(
    hash('sha256', uniqid(microtime(), true)),
    0,
    8
));

Yii::$container->set(Pagination::class, [
    'defaultPageSize' => 100,
    'pageSizeLimit' => [1, 200],
]);

Yii::$container->set(Pjax::class, [
    'scrollTo' => true,
]);
