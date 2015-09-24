<?php
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
        return $model ? $model : new static();
    }
}
