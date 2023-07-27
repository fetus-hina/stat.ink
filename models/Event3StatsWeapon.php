<?php

/**
 * @copyright Copyright (C) 2015-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "event3_stats_weapon".
 *
 * @property integer $schedule_id
 * @property integer $weapon_id
 * @property integer $battles
 * @property integer $wins
 * @property double $avg_kill
 * @property double $sd_kill
 * @property integer $min_kill
 * @property integer $p05_kill
 * @property integer $p25_kill
 * @property integer $p50_kill
 * @property integer $p75_kill
 * @property integer $p95_kill
 * @property integer $max_kill
 * @property integer $mode_kill
 * @property double $avg_assist
 * @property double $sd_assist
 * @property integer $min_assist
 * @property integer $p05_assist
 * @property integer $p25_assist
 * @property integer $p50_assist
 * @property integer $p75_assist
 * @property integer $p95_assist
 * @property integer $max_assist
 * @property integer $mode_assist
 * @property double $avg_death
 * @property double $sd_death
 * @property integer $min_death
 * @property integer $p05_death
 * @property integer $p25_death
 * @property integer $p50_death
 * @property integer $p75_death
 * @property integer $p95_death
 * @property integer $max_death
 * @property integer $mode_death
 * @property double $avg_special
 * @property double $sd_special
 * @property integer $min_special
 * @property integer $p05_special
 * @property integer $p25_special
 * @property integer $p50_special
 * @property integer $p75_special
 * @property integer $p95_special
 * @property integer $max_special
 * @property integer $mode_special
 * @property double $avg_inked
 * @property double $sd_inked
 * @property integer $min_inked
 * @property integer $p05_inked
 * @property integer $p25_inked
 * @property integer $p50_inked
 * @property integer $p75_inked
 * @property integer $p95_inked
 * @property integer $max_inked
 *
 * @property EventSchedule3 $schedule
 * @property Weapon3 $weapon
 */
class Event3StatsWeapon extends ActiveRecord
{
    public static function tableName()
    {
        return 'event3_stats_weapon';
    }

    public function rules()
    {
        return [
            [['schedule_id', 'weapon_id', 'battles', 'wins', 'min_kill', 'max_kill', 'min_assist', 'max_assist', 'min_death', 'max_death', 'min_special', 'max_special', 'min_inked', 'max_inked'], 'required'],
            [['schedule_id', 'weapon_id', 'battles', 'wins', 'min_kill', 'p05_kill', 'p25_kill', 'p50_kill', 'p75_kill', 'p95_kill', 'max_kill', 'mode_kill', 'min_assist', 'p05_assist', 'p25_assist', 'p50_assist', 'p75_assist', 'p95_assist', 'max_assist', 'mode_assist', 'min_death', 'p05_death', 'p25_death', 'p50_death', 'p75_death', 'p95_death', 'max_death', 'mode_death', 'min_special', 'p05_special', 'p25_special', 'p50_special', 'p75_special', 'p95_special', 'max_special', 'mode_special', 'min_inked', 'p05_inked', 'p25_inked', 'p50_inked', 'p75_inked', 'p95_inked', 'max_inked'], 'default', 'value' => null],
            [['schedule_id', 'weapon_id', 'battles', 'wins', 'min_kill', 'p05_kill', 'p25_kill', 'p50_kill', 'p75_kill', 'p95_kill', 'max_kill', 'mode_kill', 'min_assist', 'p05_assist', 'p25_assist', 'p50_assist', 'p75_assist', 'p95_assist', 'max_assist', 'mode_assist', 'min_death', 'p05_death', 'p25_death', 'p50_death', 'p75_death', 'p95_death', 'max_death', 'mode_death', 'min_special', 'p05_special', 'p25_special', 'p50_special', 'p75_special', 'p95_special', 'max_special', 'mode_special', 'min_inked', 'p05_inked', 'p25_inked', 'p50_inked', 'p75_inked', 'p95_inked', 'max_inked'], 'integer'],
            [['avg_kill', 'sd_kill', 'avg_assist', 'sd_assist', 'avg_death', 'sd_death', 'avg_special', 'sd_special', 'avg_inked', 'sd_inked'], 'number'],
            [['schedule_id', 'battles', 'wins', 'weapon_id'], 'unique', 'targetAttribute' => ['schedule_id', 'battles', 'wins', 'weapon_id']],
            [['schedule_id', 'weapon_id'], 'unique', 'targetAttribute' => ['schedule_id', 'weapon_id']],
            [['schedule_id'], 'exist', 'skipOnError' => true, 'targetClass' => EventSchedule3::class, 'targetAttribute' => ['schedule_id' => 'id']],
            [['weapon_id'], 'exist', 'skipOnError' => true, 'targetClass' => Weapon3::class, 'targetAttribute' => ['weapon_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'schedule_id' => 'Schedule ID',
            'weapon_id' => 'Weapon ID',
            'battles' => 'Battles',
            'wins' => 'Wins',
            'avg_kill' => 'Avg Kill',
            'sd_kill' => 'Sd Kill',
            'min_kill' => 'Min Kill',
            'p05_kill' => 'P05 Kill',
            'p25_kill' => 'P25 Kill',
            'p50_kill' => 'P50 Kill',
            'p75_kill' => 'P75 Kill',
            'p95_kill' => 'P95 Kill',
            'max_kill' => 'Max Kill',
            'mode_kill' => 'Mode Kill',
            'avg_assist' => 'Avg Assist',
            'sd_assist' => 'Sd Assist',
            'min_assist' => 'Min Assist',
            'p05_assist' => 'P05 Assist',
            'p25_assist' => 'P25 Assist',
            'p50_assist' => 'P50 Assist',
            'p75_assist' => 'P75 Assist',
            'p95_assist' => 'P95 Assist',
            'max_assist' => 'Max Assist',
            'mode_assist' => 'Mode Assist',
            'avg_death' => 'Avg Death',
            'sd_death' => 'Sd Death',
            'min_death' => 'Min Death',
            'p05_death' => 'P05 Death',
            'p25_death' => 'P25 Death',
            'p50_death' => 'P50 Death',
            'p75_death' => 'P75 Death',
            'p95_death' => 'P95 Death',
            'max_death' => 'Max Death',
            'mode_death' => 'Mode Death',
            'avg_special' => 'Avg Special',
            'sd_special' => 'Sd Special',
            'min_special' => 'Min Special',
            'p05_special' => 'P05 Special',
            'p25_special' => 'P25 Special',
            'p50_special' => 'P50 Special',
            'p75_special' => 'P75 Special',
            'p95_special' => 'P95 Special',
            'max_special' => 'Max Special',
            'mode_special' => 'Mode Special',
            'avg_inked' => 'Avg Inked',
            'sd_inked' => 'Sd Inked',
            'min_inked' => 'Min Inked',
            'p05_inked' => 'P05 Inked',
            'p25_inked' => 'P25 Inked',
            'p50_inked' => 'P50 Inked',
            'p75_inked' => 'P75 Inked',
            'p95_inked' => 'P95 Inked',
            'max_inked' => 'Max Inked',
        ];
    }

    public function getSchedule(): ActiveQuery
    {
        return $this->hasOne(EventSchedule3::class, ['id' => 'schedule_id']);
    }

    public function getWeapon(): ActiveQuery
    {
        return $this->hasOne(Weapon3::class, ['id' => 'weapon_id']);
    }
}
