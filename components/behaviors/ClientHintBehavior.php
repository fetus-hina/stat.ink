<?php

/**
 * @copyright Copyright (C) 2020-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\behaviors;

use Throwable;
use app\models\HttpClientHint;
use yii\behaviors\AttributeBehavior;
use yii\db\ActiveRecord;

class ClientHintBehavior extends AttributeBehavior
{
    public $attributes = [
        ActiveRecord::EVENT_BEFORE_INSERT => [
            'client_hint_id',
        ],
    ];

    protected function getValue($event)
    {
        try {
            if (!$model = HttpClientHint::findOrCreate()) {
                return null;
            }

            return $model->id;
        } catch (Throwable $e) {
            return null;
        }
    }
}
