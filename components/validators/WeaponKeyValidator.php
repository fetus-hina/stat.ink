<?php
/**
 * @copyright Copyright (C) 2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\components\validators;

use Yii;
use app\models\Special;
use app\models\Subweapon;
use app\models\Weapon;
use app\models\WeaponType;
use yii\validators\Validator;

class WeaponKeyValidator extends Validator
{
    const PREFIX_WEAPON_GROUP   = '@';
    const PREFIX_MAIN_WEAPON    = '~';
    const PREFIX_SUB_WEAPON     = '+';
    const PREFIX_SPECIAL_WEAPON = '*';

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
                return ($this->enableWeaponGroup) &&
                    (1 == WeaponType::find()
                        ->where(['key' => substr($value, 1)])
                        ->count());

            case static::PREFIX_MAIN_WEAPON:
                return ($this->enableMainWeapon) &&
                    (1 == Weapon::find()
                        ->where(['and',
                            ['key' => substr($value, 1)],
                            '[[id]] = [[main_group_id]]',
                        ])
                        ->count());

            case static::PREFIX_SUB_WEAPON:
                return ($this->enableSubWeapon) &&
                    (1 == Subweapon::find()
                        ->where(['key' => substr($value, 1)])
                        ->count());

            case static::PREFIX_SPECIAL_WEAPON:
                return ($this->enableSpecialWeapon) &&
                    (1 == Special::find()
                        ->where(['key' => substr($value, 1)])
                        ->count());

            default:
                return 1 == Weapon::find()
                    ->where(['key' => $value])
                    ->count();
        }
    }
}
