<?php

/**
 * @copyright Copyright (C) 2015-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

namespace app\controllers;

use Yii;
use app\actions\api\v1\BattleAction;
use app\actions\api\v1\DeathReasonAction;
use app\actions\api\v1\GearAction;
use app\actions\api\v1\MapAction;
use app\actions\api\v1\RuleAction;
use app\actions\api\v1\UserAction;
use app\actions\api\v1\WeaponAction;
use app\actions\api\v1\WeaponTrendsAction;
use app\components\web\Controller;
use yii\filters\VerbFilter;

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
                    'battle' => [
                        'delete',
                        'get',
                        'head',
                        'patch',
                        'post',
                    ],
                    '*' => [ 'head', 'get' ],
                ],
            ],
        ];
    }

    public function actions()
    {
        return [
            'battle' => [ 'class' => BattleAction::class ],
            'death-reason' => [ 'class' => DeathReasonAction::class ],
            'gear' => [ 'class' => GearAction::class ],
            'map' => [ 'class' => MapAction::class ],
            'rule' => [ 'class' => RuleAction::class ],
            'user' => [ 'class' => UserAction::class ],
            'weapon' => [ 'class' => WeaponAction::class ],
            'weapon-trends' => [ 'class' => WeaponTrendsAction::class ],
        ];
    }
}
