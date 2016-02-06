<?php
/**
 * @copyright Copyright (C) 2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\controllers;

use Yii;
use app\components\web\Controller;

class FeedController extends Controller
{
    public function actions()
    {
        $prefix = 'app\actions\feed';
        return [
            'user' => [ 'class' => $prefix . '\UserAction' ],
        ];
    }
}
