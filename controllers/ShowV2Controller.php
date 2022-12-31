<?php

/**
 * @copyright Copyright (C) 2015-2021 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\controllers;

use Yii;
use app\actions\show\v2\BattleAction;
use app\actions\show\v2\EditBattleAction;
use app\actions\show\v2\UserAction;
use app\actions\show\v2\UserStatByMapAction;
use app\actions\show\v2\UserStatByMapRuleAction;
use app\actions\show\v2\UserStatByMapRuleDetailAction;
use app\actions\show\v2\UserStatByRuleAction;
use app\actions\show\v2\UserStatByWeaponAction;
use app\actions\show\v2\UserStatCauseOfDeathAction;
use app\actions\show\v2\UserStatGachiAction;
use app\actions\show\v2\UserStatMonthlyReportAction;
use app\actions\show\v2\UserStatNawabariAction;
use app\actions\show\v2\UserStatReportAction;
use app\actions\show\v2\UserStatSplatfestAction;
use app\actions\show\v2\UserStatVsWeaponAction;
use app\actions\show\v2\UserStatWeaponAction;
use app\components\web\Controller;
use yii\filters\AccessControl;
use yii\filters\AccessRule;
use yii\filters\VerbFilter;

class ShowV2Controller extends Controller
{
    public $layout = "main";

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
            'battle' => [ 'class' => BattleAction::class ],
            'edit-battle' => [ 'class' => EditBattleAction::class ],
            'user' => [ 'class' => UserAction::class ],
            // 'user-stat-by-map' => [ 'class' => UserStatByMapAction::class ],
            'user-stat-by-map-rule' => [ 'class' => UserStatByMapRuleAction::class ],
            // 'user-stat-by-map-rule-detail' => [ 'class' => UserStatByMapRuleDetailAction::class ],
            // 'user-stat-by-rule' => [ 'class' => UserStatByRuleAction::class ],
            'user-stat-by-weapon' => [ 'class' => UserStatByWeaponAction::class ],
            // 'user-stat-cause-of-death' => [ 'class' => UserStatCauseOfDeathAction::class ],
            'user-stat-gachi' => [ 'class' => UserStatGachiAction::class ],
            'user-stat-monthly-report' => [ 'class' => UserStatMonthlyReportAction::class ],
            'user-stat-nawabari' => [ 'class' => UserStatNawabariAction::class ],
            'user-stat-report' => [ 'class' => UserStatReportAction::class ],
            'user-stat-splatfest' => [ 'class' => UserStatSplatfestAction::class ],
            // 'user-stat-vs-weapon' => [ 'class' => UserStatVsWeaponAction::class ],
            // 'user-stat-weapon' => [ 'class' => UserStatWeaponAction::class ],
        ];
    }
}
