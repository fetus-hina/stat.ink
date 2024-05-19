<?php

/**
 * @copyright Copyright (C) 2015-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\models;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "salmon_weapon3_alias".
 *
 * @property integer $id
 * @property integer $weapon_id
 * @property string $key
 *
 * @property SalmonWeapon3 $weapon
 */
class SalmonWeapon3Alias extends ActiveRecord
{
    public static function tableName()
    {
        return 'salmon_weapon3_alias';
    }

    public function rules()
    {
        return [
            [['weapon_id', 'key'], 'required'],
            [['weapon_id'], 'default', 'value' => null],
            [['weapon_id'], 'integer'],
            [['key'], 'string', 'max' => 32],
            [['key'], 'unique'],
            [['weapon_id'], 'exist', 'skipOnError' => true, 'targetClass' => SalmonWeapon3::class, 'targetAttribute' => ['weapon_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'weapon_id' => 'Weapon ID',
            'key' => 'Key',
        ];
    }

    public function getWeapon(): ActiveQuery
    {
        return $this->hasOne(SalmonWeapon3::class, ['id' => 'weapon_id']);
    }
}
