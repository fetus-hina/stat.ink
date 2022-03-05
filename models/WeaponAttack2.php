<?php

/**
 * @copyright Copyright (C) 2015-2020 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "weapon_attack2".
 *
 * @property integer $weapon_id
 * @property integer $version_id
 * @property string $damage
 * @property string $damage2
 * @property string $damage3
 *
 * @property SplatoonVersion2 $version
 * @property Weapon2 $weapon
 */
class WeaponAttack2 extends ActiveRecord
{
    public static function tableName()
    {
        return 'weapon_attack2';
    }

    public function rules()
    {
        return [
            [['weapon_id', 'version_id', 'damage'], 'required'],
            [['weapon_id', 'version_id'], 'default', 'value' => null],
            [['weapon_id', 'version_id'], 'integer'],
            [['damage', 'damage2', 'damage3'], 'number'],
            [['weapon_id', 'version_id'], 'unique',
                'targetAttribute' => ['weapon_id', 'version_id'],
            ],
            [['version_id'], 'exist',
                'skipOnError' => true,
                'targetClass' => SplatoonVersion2::class,
                'targetAttribute' => ['version_id' => 'id'],
            ],
            [['weapon_id'], 'exist',
                'skipOnError' => true,
                'targetClass' => Weapon2::class,
                'targetAttribute' => ['weapon_id' => 'id'],
            ],
        ];
    }

    public function attributeLabels()
    {
        return [
            'weapon_id' => 'Weapon ID',
            'version_id' => 'Version ID',
            'damage' => 'Damage',
            'damage2' => 'Damage2',
            'damage3' => 'Damage3',
        ];
    }

    public function getVersion(): ActiveQuery
    {
        return $this->hasOne(SplatoonVersion2::class, ['id' => 'version_id']);
    }

    public function getWeapon(): ActiveQuery
    {
        return $this->hasOne(Weapon2::class, ['id' => 'weapon_id']);
    }
}
