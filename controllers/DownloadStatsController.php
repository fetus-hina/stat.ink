<?php

/**
 * @copyright Copyright (C) 2016-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

namespace app\controllers;

use app\actions\downloadStats\IndexAction;
use app\actions\downloadStats\WeaponRuleMapAction;
use app\components\web\Controller;
use yii\filters\VerbFilter;

class DownloadStatsController extends Controller
{
    public $layout = 'main';

    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    '*' => [ 'head', 'get' ],
                ],
            ],
        ];
    }

    public function actions()
    {
        return [
            'index' => [ 'class' => IndexAction::class ],
            'weapon-rule-map' => [ 'class' => WeaponRuleMapAction::class ],
        ];
    }
}
