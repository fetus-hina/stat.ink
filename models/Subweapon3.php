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
 * This is the model class for table "subweapon3".
 *
 * @property integer $id
 * @property string $key
 * @property string $name
 *
 * @property Subweapon3Alias[] $subweapon3Aliases
 * @property Weapon3[] $weapon3s
 */
class Subweapon3 extends ActiveRecord
{
    public static function tableName()
    {
        return 'subweapon3';
    }

    public function rules()
    {
        return [
            [['key', 'name'], 'required'],
            [['key'], 'string', 'max' => 32],
            [['name'], 'string', 'max' => 48],
            [['key'], 'unique'],
            [['name'], 'unique'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'key' => 'Key',
            'name' => 'Name',
        ];
    }

    public function getSubweapon3Aliases(): ActiveQuery
    {
        return $this->hasMany(Subweapon3Alias::class, ['subweapon_id' => 'id']);
    }

    public function getWeapon3s(): ActiveQuery
    {
        return $this->hasMany(Weapon3::class, ['subweapon_id' => 'id']);
    }
}
