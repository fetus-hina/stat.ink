<?php

/**
 * @copyright Copyright (C) 2020-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

namespace app\components\web;

use Override;
use yii\web\DbSession;

use function header;
use function headers_sent;

final class Session extends DbSession
{
    #[Override]
    public function open(): void
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
