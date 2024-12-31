<?php

/**
 * @copyright Copyright (C) 2023-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\components\validators;

use Yii;
use yii\validators\Validator;

use function base64_decode;
use function base64_encode;
use function is_scalar;
use function preg_match;
use function rtrim;

final class Base64Validator extends Validator
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
        if ($this->validateValueImpl($model->$attribute)) {
            return;
        }

        $this->addError($model, $attribute, $this->message);
    }

    /**
     * @inheritdoc
     */
    protected function validateValue($value)
    {
        if ($this->validateValueImpl($value)) {
            return;
        }

        return [$this->message, []];
    }

    /**
     * @param mixed $value
     */
    private function validateValueImpl($value): bool
    {
        if ($value === null) {
            return true;
        }

        if (!is_scalar($value)) {
            return false;
        }

        $value = (string)$value;
        if (
            !preg_match(
                '/^[A-Za-z0-9+\/]+=*$/',
                $value,
            )
        ) {
            return false;
        }

        $decoded = @base64_decode($value, strict: true);
        if ($decoded === false) {
            return false;
        }

        $encoded = base64_encode($decoded);
        return rtrim($value, '=') === rtrim($encoded, '=');
    }
}
