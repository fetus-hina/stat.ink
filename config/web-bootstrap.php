<?php

/**
 * @copyright Copyright (C) 2015-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\jobs\SalmonExportJson3Job;
use app\components\jobs\SalmonStatsJob;
use app\components\jobs\UserExportJson3Job;
use app\components\jobs\UserStatsJob;
use app\models\Battle3;
use app\models\Salmon3;
use yii\base\Event;
use yii\base\ViewEvent;
use yii\base\Widget;
use yii\data\Pagination;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\Pjax;

Yii::$classMap[ArrayHelper::class] = __DIR__ . '/../components/overwrite/yii/helpers/ArrayHelper.php';
Yii::$classMap[Html::class] = __DIR__ . '/../components/overwrite/yii/helpers/Html.php';

Widget::$autoIdPrefix = sprintf('w-%s-', substr(
    hash('sha256', uniqid(microtime(), true)),
    0,
    8,
));

Yii::$container->set(Battle3::class, [
    'on afterInsert' => function (Event $ev): void {
        $model = $ev->sender;
        if ($model instanceof Battle3 && $model->user) {
            UserStatsJob::pushQueue3($model->user);
            UserExportJson3Job::pushQueue($model->user);
        }
    },
]);

Yii::$container->set(Salmon3::class, [
    'on afterInsert' => function (Event $ev): void {
        $model = $ev->sender;
        if ($model instanceof Salmon3 && $model->user) {
            SalmonStatsJob::pushQueue3($model->user);
            SalmonExportJson3Job::pushQueue($model->user);
        }
    },
]);

Yii::$container->set(Pagination::class, [
    'defaultPageSize' => 100,
    'pageSizeLimit' => [1, 200],
]);

Yii::$container->set(Pjax::class, [
    'scrollTo' => true,
]);

if (YII_ENV_DEV) {
    Yii::$container->set(View::class, [
        'on beforeRender' => function (ViewEvent $ev): void {
            Yii::beginProfile("render {$ev->viewFile}", get_class($ev->sender));
        },
        'on afterRender' => function (Event $ev): void {
            Yii::endProfile("render {$ev->viewFile}", get_class($ev->sender));
        },
    ]);
}
