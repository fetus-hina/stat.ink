<?php

/**
 * @copyright Copyright (C) 2018-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\behaviors;

use app\models\HttpUserAgent;
use yii\behaviors\AttributeBehavior;
use yii\db\ActiveRecord;

class UserAgentBehavior extends AttributeBehavior
{
    public $attributes = [
        ActiveRecord::EVENT_BEFORE_INSERT => [ 'user_agent_id' ],
    ];

    protected function getValue($event)
    {
        if (!$model = HttpUserAgent::findOrCreate()) {
            return null;
        }

        return $model->id;
    }
}
