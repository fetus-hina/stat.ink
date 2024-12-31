<?php

/**
 * @copyright Copyright (C) 2022-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\formatters\api\v3;

use app\models\AgentVariable3;

use function ksort;

use const SORT_STRING;

final class UserAgentVariableApiFormatter
{
    /**
     * @param AgentVariable3[]|null $models
     */
    public static function toJson(?array $models): array
    {
        if (!$models) {
            return [];
        }

        $results = [];
        foreach ($models as $model) {
            $results[$model->key] = $model->value;
        }
        ksort($results, SORT_STRING);
        return $results;
    }
}
