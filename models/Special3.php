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
 * This is the model class for table "special3".
 *
 * @property integer $id
 * @property string $key
 * @property string $name
 * @property integer $rank
 *
 * @property SalmonPlayer3[] $salmonPlayer3s
 * @property SalmonSpecialUse3[] $salmonSpecialUse3s
 * @property Special3Alias[] $special3Aliases
 * @property StatSpecialUse3[] $statSpecialUse3s
 * @property SalmonWave3[] $waves
 * @property Weapon3[] $weapon3s
 */
class Special3 extends ActiveRecord
{
    public static function tableName()
    {
        return 'special3';
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

    public function getSalmonPlayer3s(): ActiveQuery
    {
        return $this->hasMany(SalmonPlayer3::class, ['special_id' => 'id']);
    }

    public function getSalmonSpecialUse3s(): ActiveQuery
    {
        return $this->hasMany(SalmonSpecialUse3::class, ['special_id' => 'id']);
    }

    public function getSpecial3Aliases(): ActiveQuery
    {
        return $this->hasMany(Special3Alias::class, ['special_id' => 'id']);
    }

    public function getStatSpecialUse3s(): ActiveQuery
    {
        return $this->hasMany(StatSpecialUse3::class, ['special_id' => 'id']);
    }

    public function getWaves(): ActiveQuery
    {
        return $this->hasMany(SalmonWave3::class, ['id' => 'wave_id'])->viaTable('salmon_special_use3', ['special_id' => 'id']);
    }

    public function getWeapon3s(): ActiveQuery
    {
        return $this->hasMany(Weapon3::class, ['special_id' => 'id']);
    }
}
