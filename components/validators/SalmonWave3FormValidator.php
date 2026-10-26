<?php

/**
 * @copyright Copyright (C) 2022-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

namespace app\components\validators;

use Yii;
use app\models\api\v3\postSalmon\WaveForm;
use yii\helpers\ArrayHelper;
use yii\validators\Validator;
use Override;

use function array_shift;
use function is_array;
use function vsprintf;

final class SalmonWave3FormValidator extends Validator
{
    /**
     * @inheritdoc
     */
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
        if (!is_array($value) || !ArrayHelper::isAssociative($value)) {
            return ['{attribute} is invalid.'];
        }

        $form = Yii::createObject(WaveForm::class);
        $form->attributes = $value;
        if ($form->validate()) {
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
