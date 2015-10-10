<?php
/**
 * @copyright Copyright (C) 2015 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use app\components\web\Controller;

class ShowController extends Controller
{
    public $layout = "main.tpl";

    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    '*' => [ 'get' ],
                ],
            ],
        ];
    }

    public function actions()
    {
        $prefix = 'app\actions\show';
        return [
            'battle' => [ 'class' => $prefix . '\BattleAction' ],
            'user' => [ 'class' => $prefix . '\UserAction' ],
            'user-stat-by-map' => [ 'class' => $prefix . '\UserStatByMapAction' ],
            'user-stat-by-rule' => [ 'class' => $prefix . '\UserStatByRuleAction' ],
            'user-stat-cause-of-death' => [ 'class' => $prefix . '\UserStatCauseOfDeathAction' ],
        ];
    }
}
