<?php

/**
 * @copyright Copyright (C) 2022-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "mainweapon3".
 *
 * @property integer $id
 * @property string $key
 * @property integer $type_id
 * @property string $name
 *
 * @property WeaponType3 $type
 * @property Weapon3[] $weapon3s
 */
class Mainweapon3 extends ActiveRecord
{
    public static function tableName()
    {
        return 'mainweapon3';
    }

    public function rules()
    {
        return [
            [['key', 'type_id', 'name'], 'required'],
            [['type_id'], 'default', 'value' => null],
            [['type_id'], 'integer'],
            [['key'], 'string', 'max' => 32],
            [['name'], 'string', 'max' => 48],
            [['key'], 'unique'],
            [['name'], 'unique'],
            [['type_id'], 'exist', 'skipOnError' => true, 'targetClass' => WeaponType3::class, 'targetAttribute' => ['type_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'key' => 'Key',
            'type_id' => 'Type ID',
            'name' => 'Name',
        ];
    }

    public function getType(): ActiveQuery
    {
        return $this->hasOne(WeaponType3::class, ['id' => 'type_id']);
    }

    public function getWeapon3s(): ActiveQuery
    {
        return $this->hasMany(Weapon3::class, ['mainweapon_id' => 'id']);
    }
}
