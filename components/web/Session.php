<?php

/**
 * @copyright Copyright (C) 2015-2020 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\components\web;

use yii\web\Session as BaseSession;

class Session extends BaseSession
{
    public function open()
    {
        if ($this->getIsActive()) {
            return;
        }

        parent::open();

        if (!headers_sent()) {
            header('Cache-Control: no-store');
        }
    }
}
