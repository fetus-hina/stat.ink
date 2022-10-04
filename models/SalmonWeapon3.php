<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\models;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "salmon_weapon3".
 *
 * @property integer $id
 * @property string $key
 * @property string $name
 * @property integer $weapon_id
 *
 * @property SalmonWeapon3Alias[] $salmonWeapon3Aliases
 * @property Mainweapon3 $weapon
 */
class SalmonWeapon3 extends ActiveRecord
{
    public static function tableName()
    {
        return 'salmon_weapon3';
    }

    public function rules()
    {
        return [
            [['key', 'name'], 'required'],
            [['weapon_id'], 'default', 'value' => null],
            [['weapon_id'], 'integer'],
            [['key'], 'string', 'max' => 32],
            [['name'], 'string', 'max' => 63],
            [['key'], 'unique'],
            [['weapon_id'], 'exist', 'skipOnError' => true, 'targetClass' => Mainweapon3::class, 'targetAttribute' => ['weapon_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'key' => 'Key',
            'name' => 'Name',
            'weapon_id' => 'Weapon ID',
        ];
    }

    public function getSalmonWeapon3Aliases(): ActiveQuery
    {
        return $this->hasMany(SalmonWeapon3Alias::class, ['weapon_id' => 'id']);
    }

    public function getWeapon(): ActiveQuery
    {
        return $this->hasOne(Mainweapon3::class, ['id' => 'weapon_id']);
    }
}
