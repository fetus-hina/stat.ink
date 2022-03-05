<?php

/**
 * @copyright Copyright (C) 2015-2021 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\models;

use yii\db\ActiveRecord;

/**
 * This is the model class for table "stat_weapon".
 *
 * @property int $rule_id
 * @property int $weapon_id
 * @property int $players
 * @property int $total_kill
 * @property int $total_death
 * @property int $win_count
 * @property int $total_point
 * @property int $point_available
 *
 * @property Rule $rule
 * @property Weapon $weapon
 */
class StatWeapon extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'stat_weapon';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['rule_id', 'weapon_id', 'players', 'total_kill', 'total_death', 'win_count'], 'required'],
            [['rule_id', 'weapon_id', 'players', 'total_kill', 'total_death', 'win_count'], 'integer'],
            [['total_point', 'point_available'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'rule_id' => 'Rule ID',
            'weapon_id' => 'Weapon ID',
            'players' => 'Players',
            'total_kill' => 'Total Kill',
            'total_death' => 'Total Death',
            'win_count' => 'Win Count',
            'total_point' => 'Total Point',
            'point_available' => 'Point Available',
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
