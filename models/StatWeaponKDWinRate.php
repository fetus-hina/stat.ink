<?php

/**
 * @copyright Copyright (C) 2016-2021 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\models;

/**
 * This is the model class for table "stat_weapon_kd_win_rate".
 *
 * @property integer $rule_id
 * @property integer $map_id
 * @property integer $weapon_id
 * @property integer $kill
 * @property integer $death
 * @property integer $battle_count
 * @property integer $win_count
 *
 * @property Map $map
 * @property Rule $rule
 * @property Weapon $weapon
 */
class StatWeaponKDWinRate extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'stat_weapon_kd_win_rate';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['rule_id', 'map_id', 'weapon_id', 'kill', 'death', 'battle_count', 'win_count'], 'required'],
            [['rule_id', 'map_id', 'weapon_id', 'kill', 'death', 'battle_count', 'win_count'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'rule_id' => 'Rule ID',
            'map_id' => 'Map ID',
            'weapon_id' => 'Weapon ID',
            'kill' => 'Kill',
            'death' => 'Death',
            'battle_count' => 'Battle Count',
            'win_count' => 'Win Count',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMap()
    {
        return $this->hasOne(Map::class, ['id' => 'map_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRule()
    {
        return $this->hasOne(Rule::class, ['id' => 'rule_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getWeapon()
    {
        return $this->hasOne(Weapon::class, ['id' => 'weapon_id']);
    }
}
