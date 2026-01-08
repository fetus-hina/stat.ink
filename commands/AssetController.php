<?php

/**
 * @copyright Copyright (C) 2019-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\commands;

use app\commands\asset\CleanupAction;
use app\commands\asset\PublishAction;
use app\commands\asset\UpRevisionAction;
use yii\console\Controller;

class AssetController extends Controller
{
    /** @return array */
    public function actions()
    {
        return [
            'cleanup' => CleanupAction::class,
            'publish' => PublishAction::class,
            'up-revision' => UpRevisionAction::class,
        ];
    }
}
