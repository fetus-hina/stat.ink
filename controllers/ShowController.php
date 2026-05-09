<?php

/**
 * @copyright Copyright (C) 2015-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

namespace app\controllers;

use app\actions\show\BattleAction;
use app\actions\show\EditBattleAction;
use app\actions\show\UserAction;
use app\actions\show\UserStatByMapAction;
use app\actions\show\UserStatByMapRuleAction;
use app\actions\show\UserStatByMapRuleDetailAction;
use app\actions\show\UserStatByRuleAction;
use app\actions\show\UserStatByWeaponAction;
use app\actions\show\UserStatCauseOfDeathAction;
use app\actions\show\UserStatGachiAction;
use app\actions\show\UserStatNawabariAction;
use app\actions\show\UserStatReportAction;
use app\actions\show\UserStatVsWeaponAction;
use app\actions\show\UserStatWeaponAction;
use app\components\web\Controller;
use yii\filters\AccessControl;
use yii\filters\AccessRule;
use yii\filters\VerbFilter;

class ShowController extends Controller
{
    public $layout = 'main';

    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'edit-battle' => [ 'head', 'get', 'post' ],
                    '*' => [ 'head', 'get' ],
                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
                'only' => [ 'edit-battle' ],
                'rules' => [
                    [
                        'actions' => [ 'edit-battle' ],
                        'roles' => ['@'],
                        'allow' => true,
                    ],
                ],
                'ruleConfig' => [
                    'class' => AccessRule::className(),
                    'matchCallback' => fn ($rule, $action) => $action->isEditable,
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
            'user-stat-by-map' => [ 'class' => UserStatByMapAction::class ],
            'user-stat-by-map-rule' => [ 'class' => UserStatByMapRuleAction::class ],
            'user-stat-by-map-rule-detail' => [ 'class' => UserStatByMapRuleDetailAction::class ],
            'user-stat-by-rule' => [ 'class' => UserStatByRuleAction::class ],
            'user-stat-by-weapon' => [ 'class' => UserStatByWeaponAction::class ],
            'user-stat-cause-of-death' => [ 'class' => UserStatCauseOfDeathAction::class ],
            'user-stat-gachi' => [ 'class' => UserStatGachiAction::class ],
            'user-stat-nawabari' => [ 'class' => UserStatNawabariAction::class ],
            'user-stat-report' => [ 'class' => UserStatReportAction::class ],
            'user-stat-vs-weapon' => [ 'class' => UserStatVsWeaponAction::class ],
            'user-stat-weapon' => [ 'class' => UserStatWeaponAction::class ],
        ];
    }
}
