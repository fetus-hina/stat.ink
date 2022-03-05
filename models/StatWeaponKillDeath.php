<?php

/**
 * @copyright Copyright (C) 2016-2021 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\models;

use yii\db\ActiveRecord;

/**
 * This is the model class for table "stat_weapon_kill_death".
 *
 * @property int $weapon_id
 * @property int $rule_id
 * @property int $kill
 * @property int $death
 * @property int $battle
 * @property int $win
 *
 * @property Rule $rule
 * @property Weapon $weapon
 */
class StatWeaponKillDeath extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'stat_weapon_kill_death';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['weapon_id', 'rule_id', 'kill', 'death', 'battle', 'win'], 'required'],
            [['weapon_id', 'rule_id', 'kill', 'death', 'battle', 'win'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'weapon_id' => 'Weapon ID',
            'rule_id' => 'Rule ID',
            'kill' => 'Kill',
            'death' => 'Death',
            'battle' => 'Battle',
            'win' => 'Win',
        ];
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
