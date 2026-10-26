<?php

/**
 * @copyright Copyright (C) 2022-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

namespace app\components\validators;

use Override;
use Yii;
use app\components\helpers\TypeHelper;
use yii\db\ActiveRecord;
use yii\validators\ExistValidator;
use yii\validators\Validator;

use function is_scalar;

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

    #[Override]
    public function init(): void
    {
        parent::init();
        if ($this->message === null) {
            $this->message = Yii::t('yii', '{attribute} is invalid.');
        }
    }

    /**
     * @inheritdoc
     */
    #[Override]
    public function validateAttribute($model, $attribute): void
    {
        if ($this->validateValueImpl($model->$attribute)) {
            return;
        }

        $this->addError($model, $attribute, TypeHelper::string($this->message));
    }

    /**
     * @inheritdoc
     * @return array{string, array<string, mixed>}|null
     */
    #[Override]
    protected function validateValue($value)
    {
        if ($this->validateValueImpl($value)) {
            return null;
        }

        return [TypeHelper::string($this->message), []];
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
        if ($this->isExists($value, $this->modelClass, $this->modelClassAttribute)) {
            return true;
        }

        return $this->aliasClass !== null &&
            $this->isExists($value, $this->aliasClass, $this->aliasClassAttribute);
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
