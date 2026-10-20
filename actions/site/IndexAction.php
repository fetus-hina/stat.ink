<?php

/**
 * @copyright Copyright (C) 2015-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

namespace app\actions\site;

use Override;
use yii\web\ViewAction as BaseAction;

class IndexAction extends BaseAction
{
    #[Override]
    public function run(): string
    {
        $time = $_SERVER['REQUEST_TIME'];
        return $this->controller->render('index', [
            'enableAnniversary' => (1474729200 <= $time && $time <= 1474988400),
        ]);
    }
}
