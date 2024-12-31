<?php

/**
 * @copyright Copyright (C) 2023-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "event_schedule3".
 *
 * @property integer $id
 * @property integer $event_id
 * @property integer $rule_id
 * @property string $start_at
 * @property string $end_at
 *
 * @property Event3 $event
 * @property Event3StatsPower $event3StatsPower
 * @property Event3StatsPowerHistogram[] $event3StatsPowerHistograms
 * @property Event3StatsSpecial[] $event3StatsSpecials
 * @property Event3StatsWeapon[] $event3StatsWeapons
 * @property EventMap3[] $eventMap3s
 * @property EventPeriod3[] $eventPeriod3s
 * @property Map3[] $maps
 * @property Rule3 $rule
 * @property Special3[] $specials
 * @property Weapon3[] $weapons
 */
class EventSchedule3 extends ActiveRecord
{
    public static function tableName()
    {
        return 'event_schedule3';
    }

    public function rules()
    {
        return [
            [['event_id', 'rule_id', 'start_at', 'end_at'], 'required'],
            [['event_id', 'rule_id'], 'default', 'value' => null],
            [['event_id', 'rule_id'], 'integer'],
            [['start_at', 'end_at'], 'safe'],
            [['event_id'], 'exist', 'skipOnError' => true, 'targetClass' => Event3::class, 'targetAttribute' => ['event_id' => 'id']],
            [['rule_id'], 'exist', 'skipOnError' => true, 'targetClass' => Rule3::class, 'targetAttribute' => ['rule_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'event_id' => 'Event ID',
            'rule_id' => 'Rule ID',
            'start_at' => 'Start At',
            'end_at' => 'End At',
        ];
    }

    public function getEvent(): ActiveQuery
    {
        return $this->hasOne(Event3::class, ['id' => 'event_id']);
    }

    public function getEvent3StatsPower(): ActiveQuery
    {
        return $this->hasOne(Event3StatsPower::class, ['schedule_id' => 'id']);
    }

    public function getEvent3StatsPowerHistograms(): ActiveQuery
    {
        return $this->hasMany(Event3StatsPowerHistogram::class, ['schedule_id' => 'id']);
    }

    public function getEvent3StatsSpecials(): ActiveQuery
    {
        return $this->hasMany(Event3StatsSpecial::class, ['schedule_id' => 'id']);
    }

    public function getEvent3StatsWeapons(): ActiveQuery
    {
        return $this->hasMany(Event3StatsWeapon::class, ['schedule_id' => 'id']);
    }

    public function getEventMap3s(): ActiveQuery
    {
        return $this->hasMany(EventMap3::class, ['schedule_id' => 'id']);
    }

    public function getEventPeriod3s(): ActiveQuery
    {
        return $this->hasMany(EventPeriod3::class, ['schedule_id' => 'id']);
    }

    public function getMaps(): ActiveQuery
    {
        return $this->hasMany(Map3::class, ['id' => 'map_id'])->viaTable('event_map3', ['schedule_id' => 'id']);
    }

    public function getRule(): ActiveQuery
    {
        return $this->hasOne(Rule3::class, ['id' => 'rule_id']);
    }

    public function getSpecials(): ActiveQuery
    {
        return $this->hasMany(Special3::class, ['id' => 'special_id'])->viaTable('event3_stats_special', ['schedule_id' => 'id']);
    }

    public function getWeapons(): ActiveQuery
    {
        return $this->hasMany(Weapon3::class, ['id' => 'weapon_id'])->viaTable('event3_stats_weapon', ['schedule_id' => 'id']);
    }
}
