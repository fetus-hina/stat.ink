<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\controllers;

use Yii;
use app\actions\show\v3\BattleAction;
use app\actions\show\v3\UserAction;
use app\components\web\Controller;

final class ShowV3Controller extends Controller
{
    public function actions()
    {
        return [
            // 'battle' => BattleAction::class,
            'user' => UserAction::class,
        ];
    }
}
