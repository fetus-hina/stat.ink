<?php

/**
 * @copyright Copyright (C) 2022-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\models\api\v3\postBattle;

use LogicException;
use Yii;
use yii\base\Model;

use function is_array;

final class GearsForm extends Model
{
    public $headgear;
    public $clothing;
    public $shoes;

    public ?GearForm $headgearForm = null;
    public ?GearForm $clothingForm = null;
    public ?GearForm $shoesForm = null;

    public function rules()
    {
        return [
            [['headgear', 'clothing', 'shoes'], 'validateGear'],
        ];
    }

    public function validateGear(string $attribute)
    {
        if ($this->hasErrors($attribute)) {
            return;
        }

        $data = $this->$attribute;
        if ($data === null || $data === '') {
            return;
        }

        if (!is_array($data)) {
            $this->addError($attribute, "{$attribute} is invalid");
            return;
        }

        $form = Yii::createObject(GearForm::class);
        $form->attributes = $data;
        if ($form->validate()) {
            switch ($attribute) {
                case 'headgear':
                    $this->headgearForm = $form;
                    break;

                case 'clothing':
                    $this->clothingForm = $form;
                    break;

                case 'shoes':
                    $this->shoesForm = $form;
                    break;

                default:
                    throw new LogicException();
            }
            return;
        }

        foreach ($form->getErrors() as $key => $values) {
            foreach ($values as $value) {
                $this->addError($attribute, "{$key} {$value}");
            }
        }
    }
}
