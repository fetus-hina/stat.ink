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
 * This is the model class for table "map3".
 *
 * @property integer $id
 * @property string $key
 * @property string $name
 * @property string $short_name
 * @property integer $area
 * @property string $release_at
 *
 * @property Battle3[] $battle3s
 * @property Knockout3[] $knockout3s
 * @property Map3Alias[] $map3Aliases
 * @property Salmon3[] $salmon3s
 * @property SalmonSchedule3[] $salmonSchedule3s
 * @property ScheduleMap3[] $scheduleMap3s
 * @property StatSalmon3TideEvent[] $statSalmon3TideEvents
 */
class Map3 extends ActiveRecord
{
    public static function tableName()
    {
        return 'map3';
    }

    public function rules()
    {
        return [
            [['key', 'name', 'short_name'], 'required'],
            [['area'], 'default', 'value' => null],
            [['area'], 'integer'],
            [['release_at'], 'safe'],
            [['key'], 'string', 'max' => 32],
            [['name'], 'string', 'max' => 48],
            [['short_name'], 'string', 'max' => 16],
            [['key'], 'unique'],
            [['name'], 'unique'],
            [['short_name'], 'unique'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'key' => 'Key',
            'name' => 'Name',
            'short_name' => 'Short Name',
            'area' => 'Area',
            'release_at' => 'Release At',
        ];
    }

    public function getBattle3s(): ActiveQuery
    {
        return $this->hasMany(Battle3::class, ['map_id' => 'id']);
    }

    public function getKnockout3s(): ActiveQuery
    {
        return $this->hasMany(Knockout3::class, ['map_id' => 'id']);
    }

    public function getMap3Aliases(): ActiveQuery
    {
        return $this->hasMany(Map3Alias::class, ['map_id' => 'id']);
    }

    public function getSalmon3s(): ActiveQuery
    {
        return $this->hasMany(Salmon3::class, ['big_stage_id' => 'id']);
    }

    public function getSalmonSchedule3s(): ActiveQuery
    {
        return $this->hasMany(SalmonSchedule3::class, ['big_map_id' => 'id']);
    }

    public function getScheduleMap3s(): ActiveQuery
    {
        return $this->hasMany(ScheduleMap3::class, ['map_id' => 'id']);
    }

    public function getStatSalmon3TideEvents(): ActiveQuery
    {
        return $this->hasMany(StatSalmon3TideEvent::class, ['big_stage_id' => 'id']);
    }
}
