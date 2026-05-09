<?php

/**
 * @copyright Copyright (C) 2016-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

namespace app\controllers;

use app\actions\stage\IndexAction;
use app\actions\stage\MapAction;
use app\actions\stage\MapDetailAction;
use app\actions\stage\MapHistoryJsonAction;
use app\actions\stage\MonthAction;
use app\components\web\Controller;

class StageController extends Controller
{
    public $layout = 'main';

    public function actions()
    {
        return [
            'index' => [ 'class' => IndexAction::class ],
            'map' => [ 'class' => MapAction::class ],
            'map-detail' => [ 'class' => MapDetailAction::class ],
            'map-history-json' => [ 'class' => MapHistoryJsonAction::class ],
            'month' => [ 'class' => MonthAction::class ],
        ];
    }
}
