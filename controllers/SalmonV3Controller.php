<?php

/**
 * @copyright Copyright (C) 2022-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\controllers;

use app\actions\salmon\v3\DeleteAction;
use app\actions\salmon\v3\IndexAction;
use app\actions\salmon\v3\ViewAction;
use app\actions\salmon\v3\stats\BossesAction;
use app\actions\salmon\v3\stats\ScheduleAction;
use app\actions\salmon\v3\stats\StatsAction;
use app\components\web\Controller;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

final class SalmonV3Controller extends Controller
{
    public $layout = 'main';

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['post', 'delete'],
                    '*' => ['head', 'get'],
                ],
            ],
            'access' => [
                'class' => AccessControl::class,
                'only' => [
                    'delete',
                ],
                'rules' => [
                    [
                        'actions' => ['delete'],
                        'roles' => ['@'],
                        'allow' => true,
                    ],
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
            'delete' => DeleteAction::class,
            'index' => IndexAction::class,
            'stats-bosses' => BossesAction::class,
            'stats-schedule' => ScheduleAction::class,
            'stats-stats' => StatsAction::class,
            'view' => ViewAction::class,
        ];
    }
}
