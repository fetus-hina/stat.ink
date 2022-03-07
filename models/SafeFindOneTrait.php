<?php

/**
 * @copyright Copyright (C) 2015 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\models;

trait SafeFindOneTrait
{
    public static function safeFindById($id)
    {
        return static::safeFindOne('id', $id);
    }

    public static function safeFindByKey($key)
    {
        return static::safeFindOne('key', $key);
    }

    protected static function safeFindOne($key, $value)
    {
        $model = static::findOne([$key => $value]);
        return $model ? $model : new self();
    }
}
