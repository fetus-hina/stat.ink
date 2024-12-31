<?php

/**
 * @copyright Copyright (C) 2019-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\commands;

use app\commands\bugfix\Btl2SplatnetJsonAction;
use app\commands\bugfix\S3sKillAssistAction;
use yii\console\Controller;

final class BugFixController extends Controller
{
    public function actions()
    {
        return [
            'btl2splatnet-json' => Btl2SplatnetJsonAction::class,
            's3s-kill-assist' => S3sKillAssistAction::class,
        ];
    }
}
