<?php

/**
 * @copyright Copyright (C) 2015-2021 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\controllers;

use app\actions\show\v2\BattleAction;
use app\actions\show\v2\EditBattleAction;
use app\actions\show\v2\UserAction;
use app\actions\show\v2\UserStatByMapRuleAction;
use app\actions\show\v2\UserStatByWeaponAction;
use app\actions\show\v2\UserStatGachiAction;
use app\actions\show\v2\UserStatMonthlyReportAction;
use app\actions\show\v2\UserStatNawabariAction;
use app\actions\show\v2\UserStatReportAction;
use app\actions\show\v2\UserStatSplatfestAction;
use app\components\web\Controller;
use yii\filters\AccessControl;
use yii\filters\AccessRule;
use yii\filters\VerbFilter;

class ShowV2Controller extends Controller
{
    public $layout = 'main';

    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'edit-battle' => [ 'head', 'get', 'post' ],
                    '*' => [ 'head', 'get' ],
                ],
            ],
            'access' => [
                'class' => AccessControl::class,
                'only' => [ 'edit-battle' ],
                'rules' => [
                    [
                        'actions' => [ 'edit-battle' ],
                        'roles' => ['@'],
                        'allow' => true,
                    ],
                ],
                'ruleConfig' => [
                    'class' => AccessRule::class,
                    'matchCallback' => fn (AccessRule $rule, EditBattleAction $action): bool => (bool)$action->isEditable,
                ],
            ],
        ];
    }

    public function actions()
    {
        return [
            'battle' => BattleAction::class,
            'edit-battle' => EditBattleAction::class,
            'user' => UserAction::class,
            'user-stat-by-map-rule' => UserStatByMapRuleAction::class,
            'user-stat-by-weapon' => UserStatByWeaponAction::class,
            'user-stat-gachi' => UserStatGachiAction::class,
            'user-stat-monthly-report' => UserStatMonthlyReportAction::class,
            'user-stat-nawabari' => UserStatNawabariAction::class,
            'user-stat-report' => UserStatReportAction::class,
            'user-stat-splatfest' => UserStatSplatfestAction::class,
        ];
    }
}
