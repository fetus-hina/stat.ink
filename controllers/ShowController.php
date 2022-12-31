<?php

/**
 * @copyright Copyright (C) 2015 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\controllers;

use Yii;
use app\components\web\Controller;
use yii\filters\AccessControl;
use yii\filters\AccessRule;
use yii\filters\VerbFilter;

class ShowController extends Controller
{
    public $layout = "main";

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
        $prefix = 'app\actions\show';
        return [
            'battle' => [ 'class' => $prefix . '\BattleAction' ],
            'edit-battle' => [ 'class' => $prefix . '\EditBattleAction' ],
            'user' => [ 'class' => $prefix . '\UserAction' ],
            'user-stat-by-map' => [ 'class' => $prefix . '\UserStatByMapAction' ],
            'user-stat-by-map-rule' => [ 'class' => $prefix . '\UserStatByMapRuleAction' ],
            'user-stat-by-map-rule-detail' => [ 'class' => $prefix . '\UserStatByMapRuleDetailAction' ],
            'user-stat-by-rule' => [ 'class' => $prefix . '\UserStatByRuleAction' ],
            'user-stat-by-weapon' => [ 'class' => $prefix . '\UserStatByWeaponAction' ],
            'user-stat-cause-of-death' => [ 'class' => $prefix . '\UserStatCauseOfDeathAction' ],
            'user-stat-gachi' => [ 'class' => $prefix . '\UserStatGachiAction' ],
            'user-stat-nawabari' => [ 'class' => $prefix . '\UserStatNawabariAction' ],
            'user-stat-report' => [ 'class' => $prefix . '\UserStatReportAction' ],
            'user-stat-vs-weapon' => [ 'class' => $prefix . '\UserStatVsWeaponAction' ],
            'user-stat-weapon' => [ 'class' => $prefix . '\UserStatWeaponAction' ],
        ];
    }
}
