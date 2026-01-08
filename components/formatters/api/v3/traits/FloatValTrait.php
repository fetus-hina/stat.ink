<?php

/**
 * @copyright Copyright (C) 2023-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\formatters\api\v3\traits;

use function filter_var;
use function is_float;
use function is_int;
use function is_scalar;

use const FILTER_VALIDATE_FLOAT;

trait FloatValTrait
{
    /**
     * @var mixed $value
     */
    private static function floatVal($value): ?float
    {
        if ($value === null || !is_scalar($value)) {
            return null;
        }

        if (is_float($value) || is_int($value)) {
            return (float)$value;
        }

        $value = filter_var($value, FILTER_VALIDATE_FLOAT);
        return is_float($value) ? $value : null;
    }
}
