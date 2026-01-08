<?php

/**
 * @copyright Copyright (C) 2015-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\models\api\v1;

use yii\base\Model;

class PostGearsForm extends Model
{
    public $headgear;
    public $clothing;
    public $shoes;

    public function rules()
    {
        return [
            [['headgear'], 'validateHeadgear'],
            [['clothing'], 'validateClothing'],
            [['shoes'], 'validateShoes'],
        ];
    }

    public function validateHeadgear($attribute, $params)
    {
        return $this->validateGear(PostHeadgearForm::class, $attribute, $params);
    }

    public function validateClothing($attribute, $params)
    {
        return $this->validateGear(PostClothingForm::class, $attribute, $params);
    }

    public function validateShoes($attribute, $params)
    {
        return $this->validateGear(PostShoesForm::class, $attribute, $params);
    }

    private function validateGear($klass, $attribute, $params)
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
