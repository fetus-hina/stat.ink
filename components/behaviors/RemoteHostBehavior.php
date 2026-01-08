<?php

/**
 * @copyright Copyright (C) 2018-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\behaviors;

use Yii;
use app\components\helpers\IPHelper;
use yii\behaviors\AttributeBehavior;
use yii\db\ActiveRecord;

class RemoteHostBehavior extends AttributeBehavior
{
    public $attributes = [
        ActiveRecord::EVENT_BEFORE_INSERT => [ 'remote_host' ],
    ];

    protected function getValue($event)
    {
        return IPHelper::reverseLookup(
            Yii::$app->getRequest()->getUserIP(),
        );
    }
}
