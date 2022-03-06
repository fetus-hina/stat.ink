<?php

/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\models;

use yii\db\ActiveRecord;

/**
 * This is the model class for table "stat_weapon2_use_count_per_week".
 *
 * @property int $isoyear
 * @property int $isoweek
 * @property int $rule_id
 * @property int $weapon_id
 * @property int $battles
 * @property int $wins
 * @property int $kills
 * @property int $deaths
 * @property int $kd_available
 * @property int $kills_with_time
 * @property int $deaths_with_time
 * @property int $kd_time_available
 * @property int $kd_time_seconds
 * @property int $specials
 * @property int $specials_available
 * @property int $specials_with_time
 * @property int $specials_time_available
 * @property int $specials_time_seconds
 * @property int $inked
 * @property int $inked_available
 * @property int $inked_with_time
 * @property int $inked_time_available
 * @property int $inked_time_seconds
 * @property int $knockout_wins
 * @property int $timeup_wins
 * @property int $knockout_loses
 * @property int $timeup_loses
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
     * @return \yii\db\ActiveQuery
     */
    public function getRule()
    {
        return $this->hasOne(Rule2::class, ['id' => 'rule_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getWeapon()
    {
        return $this->hasOne(Weapon2::class, ['id' => 'weapon_id']);
    }
}
