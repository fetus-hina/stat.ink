<?php

/**
 * @copyright Copyright (C) 2022-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

namespace app\components\validators;

use Override;
use Yii;
use app\components\helpers\TypeHelper;
use app\models\api\v3\postBattle\PlayerForm;
use yii\validators\Validator;

use function array_shift;
use function is_array;
use function vsprintf;

final class BattlePlayer3FormValidator extends Validator
{
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
     * @return array{string, array<string, mixed>}|null
     */
    #[Override]
    protected function validateValue($value)
    {
        $errors = $this->validateValueImpl($value);
        if (!$errors) {
            return null;
        }

        return [array_shift($errors), []];
    }

    /**
     * @param mixed $value
     * @return string[]
     */
    private function validateValueImpl($value): array
    {
        if (!is_array($value)) {
            return [TypeHelper::string($this->message)];
        }

        $form = Yii::createObject(PlayerForm::class);
        $form->attributes = $value;
        if ($form->validate()) {
            // OK
            return [];
        }

        $result = [];
        foreach ($form->getFirstErrors() as $key => $value) {
            $result[] = vsprintf('%s: %s', [
                $key,
                $value,
            ]);
        }
        return $result;
    }
}
