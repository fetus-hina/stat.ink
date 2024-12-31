<?php

/**
 * @copyright Copyright (C) 2020-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\components\web;

use yii\web\DbSession;

use function header;
use function headers_sent;

final class Session extends DbSession
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
