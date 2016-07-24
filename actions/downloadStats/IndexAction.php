<?php
/**
 * @copyright Copyright (C) 2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\actions\downloadStats;

use Yii;
use yii\web\ViewAction;

class IndexAction extends ViewAction
{
    public function run()
    {
        return $this->controller->render('index.tpl', []);
    }
}
