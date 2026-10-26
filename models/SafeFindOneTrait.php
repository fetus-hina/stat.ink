<?php

/**
 * @copyright Copyright (C) 2015-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

namespace app\models;

trait SafeFindOneTrait
{
    public static function safeFindById(mixed $id): static
    {
        return static::safeFindOne('id', $id);
    }

    public static function safeFindByKey(mixed $key): static
    {
        return static::safeFindOne('key', $key);
    }

    protected static function safeFindOne(string $key, mixed $value): static
    {
        $model = static::findOne([$key => $value]);
        return $model ? $model : new static();
    }
}
