<?php

/**
 * @copyright Copyright (C) 2017-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\components\behaviors;

use yii\behaviors\AttributeBehavior;
use yii\db\ActiveRecord;

class RemotePortBehavior extends AttributeBehavior
{
    public $attributes = [
        ActiveRecord::EVENT_BEFORE_INSERT => [ 'remote_port' ],
    ];

    protected function getValue($event)
    {
        return $_SERVER['REMOTE_PORT'] ?? null;
    }
}
