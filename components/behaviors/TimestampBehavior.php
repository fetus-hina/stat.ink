<?php

/**
 * @copyright Copyright (C) 2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\components\behaviors;

use app\components\helpers\db\Now;
use yii\behaviors\TimestampBehavior as BaseTimestampBehavior;

class TimestampBehavior extends BaseTimestampBehavior
{
    public function init()
    {
        parent::init();
        $this->value = new Now();
    }
}
