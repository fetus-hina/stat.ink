<?php

/**
 * @copyright Copyright (C) 2016-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\actions\stage;

use yii\web\ViewAction as BaseAction;

class IndexAction extends BaseAction
{
    public function run()
    {
        // イカリング1が2017-09に死んだのでそれを最終としてそこに飛ばす
        $this->controller->redirect([
            'stage/month',
            'year' => 2017,
            'month' => 9,
        ]);
    }
}
