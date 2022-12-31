<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\components\validators;

use Yii;
use app\models\api\v3\postBattle\PlayerForm;
use yii\validators\Validator;

final class BattlePlayer3FormValidator extends Validator
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
        if (!\is_array($value)) {
            return [$this->message];
        }

        $form = Yii::createObject(PlayerForm::class);
        $form->attributes = $value;
        if ($form->validate()) {
            // OK
            return [];
        }

        $result = [];
        foreach ($form->getFirstErrors() as $key => $value) {
            $result[] = \vsprintf('%s: %s', [
                $key,
                $value,
            ]);
        }
        return $result;
    }
}
