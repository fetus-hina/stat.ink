<?php

/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "stat_weapon2_use_count_per_week".
 *
 * @property integer $isoyear
 * @property integer $isoweek
 * @property integer $rule_id
 * @property integer $weapon_id
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
class StatWeapon2UseCountPerWeek extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'stat_weapon2_use_count_per_week';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['isoyear', 'isoweek', 'rule_id', 'weapon_id', 'battles', 'wins', 'kills', 'deaths'], 'required'],
            [['kd_available', 'kills_with_time', 'deaths_with_time', 'kd_time_available'], 'required'],
            [['kd_time_seconds', 'specials', 'specials_available', 'specials_with_time'], 'required'],
            [['specials_time_available', 'specials_time_seconds', 'inked', 'inked_available'], 'required'],
            [['inked_with_time', 'inked_time_available', 'inked_time_seconds'], 'required'],
            [['isoyear', 'isoweek', 'rule_id', 'weapon_id', 'battles', 'wins', 'kills', 'deaths'], 'integer'],
            [['kd_available', 'kills_with_time', 'deaths_with_time', 'kd_time_available'], 'integer'],
            [['kd_time_seconds', 'specials', 'specials_available', 'specials_with_time'], 'integer'],
            [['specials_time_available', 'specials_time_seconds', 'inked', 'inked_available'], 'integer'],
            [['inked_with_time', 'inked_time_available', 'inked_time_seconds', 'knockout_wins'], 'integer'],
            [['timeup_wins', 'knockout_loses', 'timeup_loses'], 'integer'],
            [['rule_id'], 'exist', 'skipOnError' => true,
                'targetClass' => Rule2::class,
                'targetAttribute' => ['rule_id' => 'id'],
            ],
            [['weapon_id'], 'exist', 'skipOnError' => true,
                'targetClass' => Weapon2::class,
                'targetAttribute' => ['weapon_id' => 'id'],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'isoyear' => 'Isoyear',
            'isoweek' => 'Isoweek',
            'rule_id' => 'Rule ID',
            'weapon_id' => 'Weapon ID',
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
}
