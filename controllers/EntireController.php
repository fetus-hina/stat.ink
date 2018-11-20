<?php
/**
 * @copyright Copyright (C) 2015-2018 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\controllers;

use Yii;
use app\actions\entire\SalmonClearAction;
use app\components\web\Controller;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

class EntireController extends Controller
{
    public $layout = "main";

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
            'kd-win2'   => [ 'class' => $prefix . '\KDWin2Action' ],
            'knockout'  => [ 'class' => $prefix . '\KnockoutAction' ],
            'knockout2' => [ 'class' => $prefix . '\Knockout2Action' ],
            'salmon-clear' => [ 'class' => SalmonClearAction::class ],
            'users'     => [ 'class' => $prefix . '\UsersAction' ],
            'weapon'    => [ 'class' => $prefix . '\WeaponAction' ],
            'weapon2'   => [ 'class' => $prefix . '\Weapon2Action' ],
            'weapons'   => [ 'class' => $prefix . '\WeaponsAction' ],
            'weapons-use' => [ 'class' => $prefix . '\WeaponsUseAction' ],
            'weapons2'  => [ 'class' => $prefix . '\Weapons2Action' ],
        ];
    }
}
