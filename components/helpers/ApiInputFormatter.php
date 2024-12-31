<?php

/**
 * @copyright Copyright (C) 2018-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\helpers;

use yii\base\Component;

use function call_user_func;
use function filter_var;
use function preg_match;
use function substr;
use function trim;

use const FILTER_VALIDATE_INT;

class ApiInputFormatter extends Component
{
    public function asString($value): ?string
    {
        $value = trim((string)$value);
        if ($value === '') {
            return null;
        }

        return $value;
    }

    public function asInteger($value): ?int
    {
        $value = $this->asString($value);
        if ($value === null) {
            return null;
        }

        $value = filter_var($value, FILTER_VALIDATE_INT);
        return $value === false ? null : $value;
    }

    public function asKeyId(
        $value,
        string $class,
        string $keyColumn = 'key',
        ?string $splatnetIdColumn = null,
    ): ?int {
        $value = $this->asString($value);

        if ($splatnetIdColumn !== null && substr($value, 0, 1) === '#' && preg_match('/^#\d+$/', $value)) {
            $model = call_user_func([$class, 'findOne'], [$splatnetIdColumn => substr($value, 1)]);
        } else {
            $model = call_user_func([$class, 'findOne'], [$keyColumn => $value]);
        }
        return $model ? $model->id : null;
    }
}
