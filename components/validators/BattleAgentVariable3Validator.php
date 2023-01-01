<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\components\validators;

use Yii;
use yii\helpers\ArrayHelper;
use yii\validators\Validator;

use function array_shift;
use function is_array;
use function is_string;
use function mb_strlen;

final class BattleAgentVariable3Validator extends Validator
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
            return ['{attribute} is invalid. (must be map<string, string>)'];
        }

        foreach ($value as $k => $v) {
            if (!is_string($k) || !is_string($v)) {
                return ['{attribute} is invalid. (must be map<string, string>)'];
            }

            if (mb_strlen($k, 'UTF-8') > 63) {
                return ['{attribute} has a too-long key (max 63)'];
            }

            if (mb_strlen($v, 'UTF-8') > 255) {
                return ['{attribute} has a too-long value (max 255)'];
            }
        }

        return [];
    }
}
