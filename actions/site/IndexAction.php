<?php

/**
 * @copyright Copyright (C) 2015-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\actions\site;

use yii\web\ViewAction as BaseAction;

class IndexAction extends BaseAction
{
    public function run()
    {
        $time = $_SERVER['REQUEST_TIME'];
        return $this->controller->render('index', [
            'enableAnniversary' => (1474729200 <= $time && $time <= 1474988400),
        ]);
    }
}
