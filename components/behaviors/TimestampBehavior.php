<?php

/**
 * @copyright Copyright (C) 2016-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\components\behaviors;

use app\components\helpers\db\Now;

class TimestampBehavior extends \yii\behaviors\TimestampBehavior
{
    public function init()
    {
        parent::init();
        $this->value = new Now();
    }
}
