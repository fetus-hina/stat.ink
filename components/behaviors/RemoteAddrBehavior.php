<?php
/**
 * @copyright Copyright (C) 2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\components\behaviors;

use Yii;
use yii\behaviors\AttributeBehavior;
use yii\db\ActiveRecord;

class RemoteAddrBehavior extends AttributeBehavior
{
    public $attributes = [
        ActiveRecord::EVENT_BEFORE_INSERT => [ 'remote_addr' ],
    ];

    protected function getValue($event)
    {
        return Yii::$app->getRequest()->getUserIP();
    }
}
