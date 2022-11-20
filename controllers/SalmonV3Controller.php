<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\controllers;

use app\actions\salmon\v3\ViewAction;
use app\components\web\Controller;

final class SalmonV3Controller extends Controller
{
    public $layout = "main";

    public function actions()
    {
        return [
            'view' => ViewAction::class,
        ];
    }
}
