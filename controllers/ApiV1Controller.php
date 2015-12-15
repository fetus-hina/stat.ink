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

    public function init()
    {
        Yii::$app->language = 'en-us';
        parent::init();
    }

    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'battle' => [ 'head', 'get', 'post', 'delete' ],
                    '*' => [ 'head', 'get' ],
                ],
            ],
        ];
    }

    public function actions()
    {
        $prefix = 'app\actions\api\v1';
        return [
            'battle'        => [ 'class' => $prefix . '\BattleAction' ],
            'death-reason'  => [ 'class' => $prefix . '\DeathReasonAction' ],
            'map'           => [ 'class' => $prefix . '\MapAction' ],
            'rule'          => [ 'class' => $prefix . '\RuleAction' ],
            'weapon'        => [ 'class' => $prefix . '\WeaponAction' ],
            'user'          => [ 'class' => $prefix . '\UserAction' ],
        ];
    }
}
