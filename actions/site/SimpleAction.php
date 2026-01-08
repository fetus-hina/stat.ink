<?php

/**
 * @copyright Copyright (C) 2015-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\actions\site;

use yii\web\ViewAction as BaseAction;

class SimpleAction extends BaseAction
{
    public $view = false;
    public $params = [];

    public function run()
    {
        return $this->controller->render($this->view, $this->params);
    }
}
