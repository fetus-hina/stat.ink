<?php

/**
 * @copyright Copyright (C) 2017-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "stat_weapon2_use_count".
 *
 * @property integer $period
 * @property integer $rule_id
 * @property integer $weapon_id
 * @property integer $map_id
 * @property integer $battles
 * @property integer $wins
 * @property integer $kills
 * @property integer $deaths
 * @property integer $kd_available
 * @property integer $kills_with_time
 * @property integer $deaths_with_time
 * @property integer $kd_time_available
 * @property integer $kd_time_seconds
 * @property integer $specials
 * @property integer $specials_available
 * @property integer $specials_with_time
 * @property integer $specials_time_available
 * @property integer $specials_time_seconds
 * @property integer $inked
 * @property integer $inked_available
 * @property integer $inked_with_time
 * @property integer $inked_time_available
 * @property integer $inked_time_seconds
 * @property integer $knockout_wins
 * @property integer $timeup_wins
 * @property integer $knockout_loses
 * @property integer $timeup_loses
 *
 * @property Rule2 $rule
 * @property Weapon2 $weapon
 */
class StatWeapon2UseCount extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'stat_weapon2_use_count';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['period', 'rule_id', 'weapon_id', 'battles', 'wins', 'kills', 'deaths', 'kd_available'], 'required'],
            [['kills_with_time', 'deaths_with_time', 'kd_time_available', 'kd_time_seconds', 'specials'], 'required'],
            [['specials_available', 'specials_with_time', 'specials_time_available'], 'required'],
            [['specials_time_seconds', 'inked', 'inked_available', 'inked_with_time'], 'required'],
            [['inked_time_available', 'inked_time_seconds', 'map_id'], 'required'],
            [['period', 'rule_id', 'weapon_id', 'battles', 'wins', 'kills', 'deaths', 'kd_available'], 'integer'],
            [['kills_with_time', 'deaths_with_time', 'kd_time_available', 'kd_time_seconds', 'specials'], 'integer'],
            [['specials_available', 'specials_with_time', 'specials_time_available'], 'integer'],
            [['specials_time_seconds', 'inked', 'inked_available', 'inked_with_time'], 'integer'],
            [['inked_time_available', 'inked_time_seconds', 'knockout_wins', 'timeup_wins'], 'integer'],
            [['knockout_loses', 'timeup_loses'], 'integer'],
            [['rule_id'], 'exist', 'skipOnError' => true,
                'targetClass' => Rule2::class,
                'targetAttribute' => ['rule_id' => 'id'],
            ],
            [['weapon_id'], 'exist', 'skipOnError' => true,
                'targetClass' => Weapon2::class,
                'targetAttribute' => ['weapon_id' => 'id'],
            ],
            [['map_id'], 'exist', 'skipOnError' => true,
                'targetClass' => Map2::class,
                'targetAttribute' => ['map_id' => 'id'],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'period' => 'Period',
            'rule_id' => 'Rule ID',
            'weapon_id' => 'Weapon ID',
            'map_id' => 'Map ID',
            'battles' => 'Battles',
            'wins' => 'Wins',
            'kills' => 'Kills',
            'deaths' => 'Deaths',
            'kd_available' => 'Kd Available',
            'kills_with_time' => 'Kills With Time',
            'deaths_with_time' => 'Deaths With Time',
            'kd_time_available' => 'Kd Time Available',
            'kd_time_seconds' => 'Kd Time Seconds',
            'specials' => 'Specials',
            'specials_available' => 'Specials Available',
            'specials_with_time' => 'Specials With Time',
            'specials_time_available' => 'Specials Time Available',
            'specials_time_seconds' => 'Specials Time Seconds',
            'inked' => 'Inked',
            'inked_available' => 'Inked Available',
            'inked_with_time' => 'Inked With Time',
            'inked_time_available' => 'Inked Time Available',
            'inked_time_seconds' => 'Inked Time Seconds',
            'knockout_wins' => 'Knockout Wins',
            'timeup_wins' => 'Timeup Wins',
            'knockout_loses' => 'Knockout Loses',
            'timeup_loses' => 'Timeup Loses',
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getRule()
    {
        return $this->hasOne(Rule2::class, ['id' => 'rule_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getWeapon()
    {
        return $this->hasOne(Weapon2::class, ['id' => 'weapon_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getMap()
    {
        return $this->hasOne(Map2::class, ['id' => 'map_id']);
    }
}
