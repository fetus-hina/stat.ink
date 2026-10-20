<?php

/**
 * @copyright Copyright (C) 2016-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

namespace app\actions\downloadStats;

use Override;
use yii\web\ViewAction;

class IndexAction extends ViewAction
{
    #[Override]
    public function run(): string
    {
        return $this->controller->render('index', []);
    }
}
