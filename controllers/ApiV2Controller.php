<?php

/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\controllers;

use Yii;
use app\components\web\Controller;
use yii\filters\VerbFilter;

class ApiV2Controller extends Controller
{
    public $enableCsrfValidation = false;

    public function init()
    {
        Yii::$app->language = 'en-US';
        Yii::$app->timeZone = 'Etc/UTC';
        parent::init();
    }

    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    '*' => ['head', 'get'],
                ],
            ],
        ];
    }

    public function actions()
    {
        $prefix = 'app\actions\api\v2';
        return [
            'gear' => [
                'class' => $prefix . '\GearAction',
            ],
            'rule' => [
                'class' => $prefix . '\RuleAction',
            ],
            'stage' => [
                'class' => $prefix . '\StageAction',
            ],
            'weapon' => [
                'class' => $prefix . '\WeaponAction',
            ],
            // 'death-reason'  => [ 'class' => $prefix . '\DeathReasonAction' ],
            // 'user'          => [ 'class' => $prefix . '\UserAction' ],
            // 'weapon-trends' => [ 'class' => $prefix . '\WeaponTrendsAction' ],
        ];
    }

    public function actionMap()
    {
        return $this->redirect(
            array_merge(Yii::$app->request->get(), ['/api-v2/stage']),
            301
        );
    }
}
