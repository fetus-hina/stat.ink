<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\components\validators;

use Yii;
use app\models\Special3;
use app\models\Special3Alias;
use yii\helpers\ArrayHelper;
use yii\validators\Validator;

use function array_shift;
use function filter_var;
use function in_array;
use function is_array;
use function is_int;
use function is_string;
use function sprintf;
use function vsprintf;

use const FILTER_VALIDATE_INT;

final class SalmonSpecialUse3Validator extends Validator
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        if ($this->message === null) {
            $this->message = Yii::t('yii', '{attribute} is invalid.');
        }
    }

    /**
     * @inheritdoc
     */
    public function validateAttribute($model, $attribute)
    {
        $errors = $this->validateValueImpl($model->$attribute);
        if (!$errors) {
            return;
        }

        foreach ($errors as $error) {
            $this->addError($model, $attribute, $error);
        }
    }

    /**
     * @inheritdoc
     */
    protected function validateValue($value)
    {
        $errors = $this->validateValueImpl($value);
        if (!$errors) {
            return;
        }

        return [array_shift($errors), []];
    }

    /**
     * @param mixed $value
     * @return string[]
     */
    private function validateValueImpl($value): array
    {
        if (
            $value === null ||
            (is_array($value) && !$value) // empty array
        ) {
            return [];
        }

        if (
            !is_array($value) ||
            !ArrayHelper::isAssociative($value)
        ) {
            return ['{attribute} is invalid. (must be map<string, int>)'];
        }

        $exists = [];

        foreach ($value as $k => $v) {
            // {"nicedama": null} のような null の指定を無視する
            if ($v === null) {
                continue;
            }

            $intV = filter_var($v, FILTER_VALIDATE_INT);
            if (!is_string($k) || !is_int($intV)) {
                return ['{attribute} is invalid. (must be map<string, int>)'];
            }

            // 0 回ならどうでもいいや
            if ($intV === 0) {
                continue;
            }

            $special = self::getSpecialByKey($k);
            if (!$special) {
                return ["{attribute} is invalid. unknown special {$k}"];
            }

            if (in_array($special->key, $exists, true)) {
                return [
                    vsprintf('{attribute} is invalid. duplicate entry for %s', [
                        $special->name,
                    ]),
                ];
            }
            $exists[] = $special->key;

            if ($intV < 0 || $intV > 2) {
                return [sprintf('{attribute}.%s is out-of-range', $k)];
            }
        }

        return [];
    }

    private static function getSpecialByKey(string $key): ?Special3
    {
        $model = Special3::find()
            ->andWhere(['key' => $key])
            ->limit(1)
            ->one();
        if ($model) {
            return $model;
        }

        $model = Special3Alias::find()
            ->andWhere(['key' => $key])
            ->limit(1)
            ->one();
        if ($model) {
            return $model->special;
        }

        return null;
    }
}
