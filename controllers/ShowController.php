<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

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

final class ShowController extends Controller
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
                    'matchCallback' => fn ($rule, $action) => $action->isEditable,
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
            'user-stat-by-map' => UserStatByMapAction::class,
            'user-stat-by-map-rule' => UserStatByMapRuleAction::class,
            'user-stat-by-map-rule-detail' => UserStatByMapRuleDetailAction::class,
            'user-stat-by-rule' => UserStatByRuleAction::class,
            'user-stat-by-weapon' => UserStatByWeaponAction::class,
            'user-stat-cause-of-death' => UserStatCauseOfDeathAction::class,
            'user-stat-gachi' => UserStatGachiAction::class,
            'user-stat-nawabari' => UserStatNawabariAction::class,
            'user-stat-report' => UserStatReportAction::class,
            'user-stat-vs-weapon' => UserStatVsWeaponAction::class,
            'user-stat-weapon' => UserStatWeaponAction::class,
        ];
    }
}
