<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\models\api\v3\postBattle;

use Yii;
use yii\db\ActiveRecord;

trait TypeHelperTrait
{
    protected static function boolVal($value): ?bool
    {
        if (\is_bool($value)) {
            return $value;
        } elseif ($value === null || $value === '') {
            return null;
        } elseif ($value === 'yes') {
            return true;
        } elseif ($value === 'no') {
            return false;
        } else {
            return null;
        }
    }

    protected static function intVal($value): ?int
    {
        $value = self::strVal($value);
        if ($value === null) {
            return null;
        }

        $value = \filter_var($value, FILTER_VALIDATE_INT);
        return \is_int($value) ? $value : null;
    }

    protected static function floatVal($value): ?float
    {
        $value = self::strVal($value);
        if ($value === null) {
            return null;
        }

        $value = \filter_var($value, FILTER_VALIDATE_FLOAT);
        return \is_float($value) ? $value : null;
    }

    protected static function strVal($value): ?string
    {
        if ($value === null || !\is_scalar($value)) {
            return null;
        }

        $value = \trim((string)$value);
        return $value !== '' ? $value : null;
    }

    protected static function tsVal($value): ?string
    {
        $value = self::intVal($value);
        if (!\is_int($value)) {
            return null;
        }

        return \gmdate('Y-m-d\TH:i:sP', $value);
    }

    /**
     * @param string|null $value
     * @phpstan-param class-string<ActiveRecord> $modelClass
     * @phpstan-param class-string<ActiveRecord>|null $aliasClass
     */
    protected static function key2id(
        $value,
        string $modelClass,
        ?string $aliasClass = null,
        ?string $aliasAttr = null
    ): ?int {
        $value = self::strVal($value);
        if ($value === null) {
            return null;
        }

        $model = $modelClass::find()->andWhere(['key' => $value])->limit(1)->one();
        if ($model) {
            return (int)$model->id;
        }

        if ($aliasClass && $aliasAttr) {
            $model = $aliasClass::find()->andWhere(['key' => $value])->limit(1)->one();
            if ($model && isset($model->$aliasAttr)) {
                return (int)$model->$aliasAttr;
            }
        }

        return null;
    }
}
