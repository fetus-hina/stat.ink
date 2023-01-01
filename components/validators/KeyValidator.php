<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\components\validators;

use Yii;
use yii\db\ActiveRecord;
use yii\validators\ExistValidator;
use yii\validators\Validator;

final class KeyValidator extends Validator
{
    /**
     * @phpstan-var class-string<ActiveRecord>
     */
    public string $modelClass;

    /**
     * @phpstan-var class-string<ActiveRecord>|null
     */
    public ?string $aliasClass = null;

    public string $modelClassAttribute = 'key';
    public string $aliasClassAttribute = 'key';

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

        if (!\is_scalar($value)) {
            return false;
        }

        $value = (string)$value;
        if ($this->isExists($value, $this->modelClass, $this->modelClassAttribute)) {
            return true;
        }

        return $this->aliasClass !== null &&
            $this->isExists($value, $this->aliasClass, $this->aliasClassAttribute)
        ;
    }

    /**
     * @phpstan-param class-string<ActiveRecord> $modelClass
     */
    private function isExists(string $value, string $modelClass, string $targetAttribute): bool
    {
        $tmpError = null;
        $validator = Yii::createObject([
            'class' => ExistValidator::class,
            'targetClass' => $modelClass,
            'targetAttribute' => $targetAttribute,
            'message' => 'error',
        ]);
        return $validator->validate($value, $tmpError);
    }
}
