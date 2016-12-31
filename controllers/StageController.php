<?php
/**
 * @copyright Copyright (C) 2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\controllers;

use Yii;
use app\components\web\Controller;

class StageController extends Controller
{
    public $layout = "main.tpl";

    public function actions()
    {
        $prefix = 'app\actions\stage';
        return [
            'index' => [ 'class' => "{$prefix}\\IndexAction" ],
            'map' => [ 'class' => "{$prefix}\\MapAction" ],
            'map-detail' => [ 'class' => "{$prefix}\\MapDetailAction" ],
            'map-history-json' => [ 'class' => "{$prefix}\\MapHistoryJsonAction" ],
            'month' => [ 'class' => "{$prefix}\\MonthAction" ],
        ];
    }
}
