<?php

/**
 * @copyright Copyright (C) 2018-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

namespace app\components\behaviors;

use Yii;
use app\components\helpers\IPHelper;
use yii\behaviors\AttributeBehavior;
use yii\db\ActiveRecord;
use yii\web\Request as WebRequest;

class RemoteHostBehavior extends AttributeBehavior
{
    /**
     * @var array<string, string[]>
     */
    public $attributes = [
        ActiveRecord::EVENT_BEFORE_INSERT => [ 'remote_host' ],
    ];

    protected function getValue($event)
    {
        $request = Yii::$app->getRequest();
        return IPHelper::reverseLookup(
            $request instanceof WebRequest ? $request->getUserIP() : null,
        );
    }
}
