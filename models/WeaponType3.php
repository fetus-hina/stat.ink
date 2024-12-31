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
 * This is the model class for table "weapon_type3".
 *
 * @property integer $id
 * @property string $key
 * @property string $name
 * @property integer $rank
 *
 * @property Mainweapon3[] $mainweapon3s
 */
class WeaponType3 extends ActiveRecord
{
    public static function tableName()
    {
        return 'weapon_type3';
    }

    public function rules()
    {
        return [
            [['key', 'name', 'rank'], 'required'],
            [['rank'], 'default', 'value' => null],
            [['rank'], 'integer'],
            [['key'], 'string', 'max' => 32],
            [['name'], 'string', 'max' => 48],
            [['key'], 'unique'],
            [['name'], 'unique'],
            [['rank'], 'unique'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'key' => 'Key',
            'name' => 'Name',
            'rank' => 'Rank',
        ];
    }

    public function getMainweapon3s(): ActiveQuery
    {
        return $this->hasMany(Mainweapon3::class, ['type_id' => 'id']);
    }
}
