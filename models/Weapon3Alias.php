<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "weapon3_alias".
 *
 * @property integer $id
 * @property integer $weapon_id
 * @property string $key
 *
 * @property Weapon3 $weapon
 */
class Weapon3Alias extends ActiveRecord
{
    public static function tableName()
    {
        return 'weapon3_alias';
    }

    public function rules()
    {
        return [
            [['weapon_id', 'key'], 'required'],
            [['weapon_id'], 'default', 'value' => null],
            [['weapon_id'], 'integer'],
            [['key'], 'string', 'max' => 32],
            [['key'], 'unique'],
            [['weapon_id', 'key'], 'unique', 'targetAttribute' => ['weapon_id', 'key']],
            [['weapon_id'], 'exist', 'skipOnError' => true, 'targetClass' => Weapon3::class, 'targetAttribute' => ['weapon_id' => 'id']],
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
        return $this->hasOne(Weapon3::class, ['id' => 'weapon_id']);
    }
}
