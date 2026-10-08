<?php

/**
 * @copyright Copyright (C) 2016-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

namespace app\components\validators;

use yii\validators\FilterValidator;

use function idn_to_ascii;
use function preg_replace_callback;
use function str_contains;
use function strtolower;

class IdnToPunycodeFilterValidator extends FilterValidator
{
    public function init()
    {
        $this->filter = function ($value) {
            if (!str_contains($value, '/')) {
                return $value |> idn_to_ascii(...) |> strtolower(...);
            }
            if (str_contains($value, '//')) {
                return preg_replace_callback(
                    '!(?<=//)([^/:]+)!',
                    fn ($match) => $match[1] |> idn_to_ascii(...) |> strtolower(...),
                    $value,
                    1,
                );
            }
            return preg_replace_callback(
                '!^([^/:]+)!',
                fn ($match) => $match[1] |> idn_to_ascii(...) |> strtolower(...),
                $value,
                1,
            );
        };
        parent::init();
    }
}
