<?php

/**
 * @copyright Copyright (C) 2015-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\controllers;

use app\actions\api\internal\ActivityAction;
use app\actions\api\internal\BlogEntryAction;
use app\actions\api\internal\CounterAction;
use app\actions\api\internal\CurrentData2Action;
use app\actions\api\internal\CurrentDataAction;
use app\actions\api\internal\GuessTimezoneAction;
use app\actions\api\internal\LatestBattlesAction;
use app\actions\api\internal\MyLatestBattlesAction;
use app\actions\api\internal\PatchBattleAction;
use app\actions\api\internal\SalmonStats2Action;
use app\actions\api\internal\ScheduleAction;
use app\actions\api\internal\v3\OgpProfile3Action;
use app\actions\api\internal\v3\PatchBattle3UrlAction;
use app\actions\api\internal\v3\PatchSalmon3UrlAction;
use app\components\web\Controller;
use yii\filters\VerbFilter;

final class ApiInternalController extends Controller
{
    /**
     * @var bool
     */
    public $enableCsrfValidation = false;

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'patch-battle' => ['patch'],
                    'patch-battle3-url' => ['patch'],
                    'patch-salmon3-url' => ['patch'],
                    '*' => ['get'],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'activity' => ActivityAction::class,
            'blog-entry' => BlogEntryAction::class,
            'counter' => CounterAction::class,
            'current-data' => CurrentDataAction::class,
            'current-data2' => CurrentData2Action::class,
            'guess-timezone' => GuessTimezoneAction::class,
            'latest-battles' => LatestBattlesAction::class,
            'my-latest-battles' => MyLatestBattlesAction::class,
            'ogp-profile3' => OgpProfile3Action::class,
            'patch-battle' => PatchBattleAction::class,
            'patch-battle3-url' => PatchBattle3UrlAction::class,
            'patch-salmon3-url' => PatchSalmon3UrlAction::class,
            'salmon-stats2' => SalmonStats2Action::class,
            'schedule' => ScheduleAction::class,
        ];
    }
}
