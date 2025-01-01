<?php

/**
 * @copyright Copyright (C) 2016-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\components\validators;

use Yii;
use app\models\Special;
use app\models\Subweapon;
use app\models\Weapon;
use app\models\WeaponType;
use yii\validators\Validator;

use function substr;

class WeaponKeyValidator extends Validator
{
    public const PREFIX_WEAPON_GROUP = '@';
    public const PREFIX_MAIN_WEAPON = '~';
    public const PREFIX_SUB_WEAPON = '+';
    public const PREFIX_SPECIAL_WEAPON = '*';

    public $enableWeaponGroup = true;
    public $enableMainWeapon = true;
    public $enableSubWeapon = true;
    public $enableSpecialWeapon = true;

    public function init()
    {
        parent::init();
        if ($this->message === null) {
            $this->message = Yii::t('yii', '{attribute} is invalid.');
        }
    }

    protected function validateValue($value)
    {
        if ($this->validateValueImpl($value)) {
            return;
        }
        return [$this->message, []];
    }

    private function validateValueImpl($value)
    {
        switch (substr($value, 0, 1)) {
            case static::PREFIX_WEAPON_GROUP:
                return $this->enableWeaponGroup &&
                    (WeaponType::find()
                        ->where(['key' => substr($value, 1)])
                        ->count() == 1);

            case static::PREFIX_MAIN_WEAPON:
                return $this->enableMainWeapon &&
                    (Weapon::find()
                        ->where(['and',
                            ['key' => substr($value, 1)],
                            '[[id]] = [[main_group_id]]',
                        ])
                        ->count() == 1);

            case static::PREFIX_SUB_WEAPON:
                return $this->enableSubWeapon &&
                    (Subweapon::find()
                        ->where(['key' => substr($value, 1)])
                        ->count() == 1);

            case static::PREFIX_SPECIAL_WEAPON:
                return $this->enableSpecialWeapon &&
                    (Special::find()
                        ->where(['key' => substr($value, 1)])
                        ->count() == 1);

            default:
                return Weapon::find()
                    ->where(['key' => $value])
                    ->count() == 1;
        }
    }
}
