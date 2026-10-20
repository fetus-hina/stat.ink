<?php

/**
 * @copyright Copyright (C) 2015-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

namespace app\models\api\v1;

use yii\base\Model;

class PostGearsForm extends Model
{
    /** @var mixed */
    public $headgear;
    /** @var mixed */
    public $clothing;
    /** @var mixed */
    public $shoes;

    public function rules()
    {
        return [
            [['headgear'], 'validateHeadgear'],
            [['clothing'], 'validateClothing'],
            [['shoes'], 'validateShoes'],
        ];
    }

    public function validateHeadgear(string $attribute, mixed $params): void
    {
        $this->validateGear(PostHeadgearForm::class, $attribute, $params);
    }

    public function validateClothing(string $attribute, mixed $params): void
    {
        $this->validateGear(PostClothingForm::class, $attribute, $params);
    }

    public function validateShoes(string $attribute, mixed $params): void
    {
        $this->validateGear(PostShoesForm::class, $attribute, $params);
    }

    /**
     * @param class-string<BaseGearForm> $klass
     */
    private function validateGear(string $klass, string $attribute, mixed $params): void
    {
        if ($this->hasErrors($attribute)) {
            return;
        }
        $form = new $klass();
        $form->attributes = $this->$attribute;
        if ($form->validate()) {
            $this->$attribute = $form;
            return;
        }
        foreach ($form->getErrors() as $key => $values) {
            foreach ($values as $value) {
                $this->addError($attribute, "{$key} {$value}");
            }
        }
    }
}
