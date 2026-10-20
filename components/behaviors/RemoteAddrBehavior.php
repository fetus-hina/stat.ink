<?php

/**
 * @copyright Copyright (C) 2017-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

namespace app\components\behaviors;

use Yii;
use yii\behaviors\AttributeBehavior;
use yii\db\ActiveRecord;
use yii\web\Request as WebRequest;

class RemoteAddrBehavior extends AttributeBehavior
{
    /**
     * @var array<string, string[]>
     */
    public $attributes = [
        ActiveRecord::EVENT_BEFORE_INSERT => [ 'remote_addr' ],
    ];

    protected function getValue($event)
    {
        $request = Yii::$app->getRequest();
        return $request instanceof WebRequest ? $request->getUserIP() : null;
    }
}
