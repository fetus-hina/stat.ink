<?php

/**
 * @copyright Copyright (C) 2022-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\formatters\api\v3;

use app\models\Agent;
use app\models\AgentVariable3;

final class UserAgentApiFormatter
{
    /**
     * @param AgentVariable3[]|null $variables
     */
    public static function toJson(
        ?Agent $model,
        ?array $variables,
        bool $fullTranslate = false,
    ): ?array {
        if (!$model && !$variables) {
            return null;
        }

        return [
            'name' => $model ? $model->name : null,
            'version' => $model ? $model->version : null,
            'variables' => UserAgentVariableApiFormatter::toJson($variables),
        ];
    }
}
