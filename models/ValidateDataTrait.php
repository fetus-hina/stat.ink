<?php

/**
 * @copyright Copyright (C) 2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

namespace app\models;

/**
 * Lets a named Model replace DynamicModel::validateData().
 */
trait ValidateDataTrait
{
    /**
     * Creates the model with the given attribute values and validates it,
     * the same way DynamicModel::validateData() does.
     *
     * @param array<string, mixed> $data
     */
    public static function validateData(array $data): static
    {
        $model = new static($data);
        $model->validate();
        return $model;
    }
}
