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

class ApiV1Controller extends Controller
{
    public $enableCsrfValidation = false;

    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'battle' => [ 'post' ],
                    '*' => [ 'get' ],
                ],
            ],
        ];
    }

    public function actions()
    {
        $prefix = 'app\actions\api\v1';
        return [
            'rule'      => [ 'class' => $prefix . '\RuleAction' ],
            'map'       => [ 'class' => $prefix . '\MapAction' ],
            'weapon'    => [ 'class' => $prefix . '\WeaponAction' ],
            'battle'    => [ 'class' => $prefix . '\BattleAction' ],
        ];
    }
}
