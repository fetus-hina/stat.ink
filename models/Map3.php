<?php

/**
 * @copyright Copyright (C) 2022-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\models;

use Override;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "map3".
 *
 * @property integer $id
 * @property string $key
 * @property string $name
 * @property string $short_name
 * @property integer $area
 * @property string $release_at
 * @property boolean $bigrun
 *
 * @property Battle3[] $battle3s
 * @property EventMap3[] $eventMap3s
 * @property Knockout3Histogram[] $knockout3Histograms
 * @property Knockout3[] $knockout3s
 * @property Map3Alias[] $map3Aliases
 * @property ScheduleMap3[] $scheduleMap3s
 * @property EventSchedule3[] $schedules
 */
class Map3 extends ActiveRecord
{
    public static function tableName()
    {
        return 'map3';
    }

    #[Override]
    public function rules()
    {
        return [
            [['area', 'release_at'], 'default', 'value' => null],
            [['bigrun'], 'default', 'value' => 0],
            [['key', 'name', 'short_name'], 'required'],
            [['area'], 'default', 'value' => null],
            [['area'], 'integer'],
            [['release_at'], 'safe'],
            [['bigrun'], 'boolean'],
            [['key'], 'string', 'max' => 32],
            [['name'], 'string', 'max' => 48],
            [['short_name'], 'string', 'max' => 16],
            [['key'], 'unique'],
            [['name'], 'unique'],
            [['short_name'], 'unique'],
        ];
    }

    #[Override]
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'key' => 'Key',
            'name' => 'Name',
            'short_name' => 'Short Name',
            'area' => 'Area',
            'release_at' => 'Release At',
            'bigrun' => 'Bigrun',
        ];
    }

    public function getBattle3s(): ActiveQuery
    {
        return $this->hasMany(Battle3::class, ['map_id' => 'id']);
    }

    public function getEventMap3s(): ActiveQuery
    {
        return $this->hasMany(EventMap3::class, ['map_id' => 'id']);
    }

    public function getKnockout3Histograms(): ActiveQuery
    {
        return $this->hasMany(Knockout3Histogram::class, ['map_id' => 'id']);
    }

    public function getKnockout3s(): ActiveQuery
    {
        return $this->hasMany(Knockout3::class, ['map_id' => 'id']);
    }

    public function getMap3Aliases(): ActiveQuery
    {
        return $this->hasMany(Map3Alias::class, ['map_id' => 'id']);
    }

    public function getScheduleMap3s(): ActiveQuery
    {
        return $this->hasMany(ScheduleMap3::class, ['map_id' => 'id']);
    }

    public function getSchedules(): ActiveQuery
    {
        return $this->hasMany(EventSchedule3::class, ['id' => 'schedule_id'])->viaTable('event_map3', ['map_id' => 'id']);
    }
}
