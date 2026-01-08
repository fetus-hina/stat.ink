<?php

/**
 * @copyright Copyright (C) 2022-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\models\api\v3\postBattle;

use app\models\Event3;
use app\models\EventPeriod3;
use yii\db\ActiveRecord;

use function filter_var;
use function floor;
use function gmdate;
use function is_bool;
use function is_float;
use function is_int;
use function is_scalar;
use function is_string;
use function preg_match;
use function strlen;
use function strtolower;
use function trim;

use const FILTER_VALIDATE_FLOAT;
use const FILTER_VALIDATE_INT;

trait TypeHelperTrait
{
    protected static function boolVal($value): ?bool
    {
        if (is_bool($value)) {
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

    protected static function colorVal($value): ?string
    {
        $value = self::strVal($value);
        if ($value === null) {
            return null;
        }

        if (!preg_match('/^[0-9a-f]{6}(?:[0-9a-f]{2})?$/i', $value)) {
            return null;
        }

        return strtolower(strlen($value) === 8 ? $value : "{$value}ff");
    }

    protected static function intVal($value): ?int
    {
        $value = self::strVal($value);
        if ($value === null) {
            return null;
        }

        $value = filter_var($value, FILTER_VALIDATE_INT);
        return is_int($value) ? $value : null;
    }

    protected static function floatVal($value): ?float
    {
        $value = self::strVal($value);
        if ($value === null) {
            return null;
        }

        $value = filter_var($value, FILTER_VALIDATE_FLOAT);
        return is_float($value) ? $value : null;
    }

    protected static function powerVal($value): ?float
    {
        $value = self::floatVal($value);
        if ($value === null) {
            return null;
        }

        // 0.1 未満の数字は切り捨てる
        // https://github.com/fetus-hina/stat.ink/issues/1189
        return floor($value * 10.0) / 10.0;
    }

    protected static function strVal($value): ?string
    {
        if ($value === null || !is_scalar($value)) {
            return null;
        }

        $value = trim((string)$value);
        return $value !== '' ? $value : null;
    }

    protected static function tsVal($value): ?string
    {
        $value = self::intVal($value);
        if (!is_int($value)) {
            return null;
        }

        return gmdate('Y-m-d\TH:i:sP', $value);
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
        ?string $aliasAttr = null,
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

    protected static function eventIdVal(?string $idB64Str, ?int $startAt): ?int
    {
        if (is_string($idB64Str)) {
            $model = Event3::find()
                ->andWhere(['internal_id' => $idB64Str])
                ->limit(1)
                ->one();
            if ($model) {
                return (int)$model->id;
            }
        }

        if ($startAt === null) {
            return null;
        }

        $model = EventPeriod3::find()
            ->with(['schedule'], true)
            ->andWhere(['and',
                ['<=', 'start_at', self::tsVal($startAt)],
                ['>', 'end_at', self::tsVal($startAt)],
            ])
            ->limit(1)
            ->one();

        return self::intVal($model?->schedule?->event_id);
    }
}
