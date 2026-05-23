<?php

/**
 * @copyright Copyright (C) 2019-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

namespace app\commands;

use app\commands\bugfix\Btl2SplatnetJsonAction;
use app\commands\bugfix\EqualsInFilenameAction;
use app\commands\bugfix\S3sKillAssistAction;
use yii\console\Controller;

final class BugFixController extends Controller
{
    public bool $dryRun = false;

    public function actions()
    {
        return [
            'btl2splatnet-json' => Btl2SplatnetJsonAction::class,
            'equals-in-filename' => EqualsInFilenameAction::class,
            's3s-kill-assist' => S3sKillAssistAction::class,
        ];
    }

    public function options($actionID)
    {
        $options = parent::options($actionID);
        if ($actionID === 'equals-in-filename') {
            $options[] = 'dryRun';
        }
        return $options;
    }
}
