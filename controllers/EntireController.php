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

class EntireController extends Controller
{
    public $layout = "main.tpl";

    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    '*' => [ 'head', 'get' ],
                ],
            ],
        ];
    }

    public function actions()
    {
        $prefix = 'app\actions\entire';
        return [
            'agent'     => [ 'class' => $prefix . '\AgentAction' ],
            'combined-agent' => [ 'class' => $prefix . '\CombinedAgentAction' ],
            'kd-win'    => [ 'class' => $prefix . '\KDWinAction' ],
            'knockout'  => [ 'class' => $prefix . '\KnockoutAction' ],
            'users'     => [ 'class' => $prefix . '\UsersAction' ],
            'weapon'    => [ 'class' => $prefix . '\WeaponAction' ],
            'weapons'   => [ 'class' => $prefix . '\WeaponsAction' ],
            'weapons-use' => [ 'class' => $prefix . '\WeaponsUseAction' ],
        ];
    }
}
