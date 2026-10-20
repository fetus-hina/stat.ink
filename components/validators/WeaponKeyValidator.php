<?php

/**
 * @copyright Copyright (C) 2016-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

namespace app\components\validators;

use Override;
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

    public bool $enableWeaponGroup = true;
    public bool $enableMainWeapon = true;
    public bool $enableSubWeapon = true;
    public bool $enableSpecialWeapon = true;

    #[Override]
    public function init(): void
    {
        parent::init();
        if ($this->message === null) {
            $this->message = Yii::t('yii', '{attribute} is invalid.');
        }
    }

    /**
     * @return array{string, array<string, mixed>}|null
     */
    #[Override]
    protected function validateValue($value)
    {
        if ($this->validateValueImpl($value)) {
            return null;
        }
        return [$this->message, []];
    }

    private function validateValueImpl(mixed $value): bool
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
