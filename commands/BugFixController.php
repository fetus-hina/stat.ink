<?php

/**
 * @copyright Copyright (C) 2015-2019 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\commands;

use app\commands\bugfix\Btl2SplatnetJsonAction;
use yii\console\Controller;

class BugFixController extends Controller
{
    public function actions()
    {
        return [
            'btl2splatnet-json' => Btl2SplatnetJsonAction::class,
        ];
    }
}
