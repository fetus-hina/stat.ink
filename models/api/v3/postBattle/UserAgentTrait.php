<?php

/**
 * @copyright Copyright (C) 2022-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\models\api\v3\postBattle;

use Yii;
use app\components\helpers\CriticalSection;
use app\models\Agent;

trait UserAgentTrait
{
    // use TypeHelperTrait;

    protected static function userAgent(?string $agentName, ?string $agentVersion): ?int
    {
        $agentName = self::strVal($agentName);
        $agentVersion = self::strVal($agentVersion);
        if ($agentName === null || $agentVersion === null) {
            return null;
        }

        $model = Agent::find()
            ->andWhere([
                'name' => $agentName,
                'version' => $agentVersion,
            ])
            ->limit(1)
            ->one();
        if (!$model) {
            if (!$lock = CriticalSection::lock(Agent::class, 60)) {
                return null;
            }
            try {
                $model = Yii::createObject([
                    'class' => Agent::class,
                    'name' => $agentName,
                    'version' => $agentVersion,
                ]);

                if (!$model->save()) {
                    return null;
                }
            } finally {
                unset($lock);
            }
        }

        return (int)$model->id;
    }
}
