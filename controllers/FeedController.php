<?php

/**
 * @copyright Copyright (C) 2016-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

namespace app\controllers;

use app\actions\feed\User2Action;
use app\actions\feed\UserAction;
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
            308,
        );
    }

    public function actions()
    {
        return [
            'user' => [ 'class' => UserAction::class ],
            'user-v2' => [ 'class' => User2Action::class ],
        ];
    }
}
