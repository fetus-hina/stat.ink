<?php

/**
 * @copyright Copyright (C) 2016-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\actions\downloadStats;

use yii\web\ViewAction;

class IndexAction extends ViewAction
{
    public function run()
    {
        return $this->controller->render('index', []);
    }
}
