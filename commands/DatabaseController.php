<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\commands;

use app\commands\database\VacuumAction;
use yii\console\Controller;

final class DatabaseController extends Controller
{
    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'vacuum' => VacuumAction::class,
        ];
    }
}
