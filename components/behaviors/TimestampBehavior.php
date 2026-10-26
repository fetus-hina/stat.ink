<?php

/**
 * @copyright Copyright (C) 2016-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

namespace app\components\behaviors;

use Override;
use app\components\helpers\db\Now;

class TimestampBehavior extends \yii\behaviors\TimestampBehavior
{
    #[Override]
    public function init(): void
    {
        parent::init();
        $this->value = new Now();
    }
}
