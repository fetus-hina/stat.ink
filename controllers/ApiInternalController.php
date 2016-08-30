<?php
/**
 * @copyright Copyright (C) 2015 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\controllers;

use Yii;
use yii\filters\VerbFilter;
use app\components\web\Controller;

class ApiInternalController extends Controller
{
    public $enableCsrfValidation = false;

    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'patch-battle' => [ 'patch' ],
                    '*' => [ 'get' ],
                ],
            ],
        ];
    }

    public function actions()
    {
        $prefix = 'app\actions\api\internal';
        return [
            'activity' => [ 'class' => $prefix . '\ActivityAction' ],
            'counter' => [ 'class' => $prefix . '\CounterAction' ],
            'current-data' => [ 'class' => $prefix . '\CurrentDataAction' ],
            'patch-battle' => [ 'class' => $prefix . '\PatchBattleAction' ],
            'recent-battles' => [ 'class' => $prefix . '\RecentBattlesAction' ],
        ];
    }
}
