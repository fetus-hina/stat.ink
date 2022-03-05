<?php

/**
 * @copyright Copyright (C) 2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\controllers;

use app\components\web\Controller;

class FeedController extends Controller
{
    public function actionCompatUser($screen_name, $lang, $type)
    {
        return $this->redirect(
            [
                '/feed/user',
                'screen_name' => $screen_name,
                'lang' => $lang,
                'type' => $type,
            ],
            308
        );
    }

    public function actions()
    {
        $prefix = 'app\actions\feed';
        return [
            'user' => [ 'class' => $prefix . '\UserAction' ],
            'user-v2' => [ 'class' => $prefix . '\User2Action' ],
        ];
    }
}
