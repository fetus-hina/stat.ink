<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\models\api\v3\postBattle;

use Yii;
use app\components\helpers\CriticalSection;
use app\models\AgentVariable3;

trait AgentVariableTrait
{
    protected function findOrCreateAgentVariable(string $key, string $value): ?int
    {
        // use double-checking lock pattern
        //
        // 1. find data without lock (fast, the data already exists)
        // In most cases, we'll find them here.
        $model = AgentVariable3::findOne([
            'key' => $key,
            'value' => $value,
        ]);
        if (!$model) {
            // 2. lock if not found
            if (!$lock = CriticalSection::lock(AgentVariable3::class, 60)) {
                return null;
            }
            try {
                // 3. find data again with lock (it may created on another process/thread)
                $model = AgentVariable3::findOne([
                    'key' => $key,
                    'value' => $value,
                ]);
                if (!$model) {
                    // 4. create new data with lock (it's new!)
                    $model = Yii::createObject([
                        'class' => AgentVariable3::class,
                        'key' => $key,
                        'value' => $value,
                    ]);
                    if (!$model->save()) {
                        return null;
                    }
                }
            } finally {
                unset($lock);
            }
        }

        return (int)$model->id;
    }
}
