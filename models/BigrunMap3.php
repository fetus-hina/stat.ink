<?php

/**
 * @copyright Copyright (C) 2024-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\models;

use Override;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "bigrun_map3".
 *
 * @property integer $id
 * @property string $key
 * @property string $name
 * @property string $short_name
 * @property integer $area
 * @property string $release_at
 * @property boolean $bigrun
 *
 * @property BigrunMap3Alias[] $bigrunMap3Aliases
 * @property Salmon3[] $salmon3s
 * @property SalmonSchedule3[] $salmonSchedule3s
 * @property StatSalmon3MapKingTide[] $statSalmon3MapKingTides
 * @property StatSalmon3MapKing[] $statSalmon3MapKings
 * @property StatSalmon3TideEvent[] $statSalmon3TideEvents
 */
class BigrunMap3 extends ActiveRecord
{
    public static function tableName()
    {
        return 'bigrun_map3';
    }

    #[Override]
    public function rules()
    {
        return [
            [['area', 'release_at'], 'default', 'value' => null],
            [['bigrun'], 'default', 'value' => 1],
            [['key', 'name', 'short_name'], 'required'],
            [['area'], 'default', 'value' => null],
            [['area'], 'integer'],
            [['release_at'], 'safe'],
            [['bigrun'], 'boolean'],
            [['key'], 'string', 'max' => 32],
            [['name'], 'string', 'max' => 48],
            [['short_name'], 'string', 'max' => 16],
            [['key'], 'unique'],
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

    public function getBigrunMap3Aliases(): ActiveQuery
    {
        return $this->hasMany(BigrunMap3Alias::class, ['map_id' => 'id']);
    }

    public function getSalmon3s(): ActiveQuery
    {
        return $this->hasMany(Salmon3::class, ['big_stage_id' => 'id']);
    }

    public function getSalmonSchedule3s(): ActiveQuery
    {
        return $this->hasMany(SalmonSchedule3::class, ['big_map_id' => 'id']);
    }

    public function getStatSalmon3MapKingTides(): ActiveQuery
    {
        return $this->hasMany(StatSalmon3MapKingTide::class, ['big_map_id' => 'id']);
    }

    public function getStatSalmon3MapKings(): ActiveQuery
    {
        return $this->hasMany(StatSalmon3MapKing::class, ['big_map_id' => 'id']);
    }

    public function getStatSalmon3TideEvents(): ActiveQuery
    {
        return $this->hasMany(StatSalmon3TideEvent::class, ['big_stage_id' => 'id']);
    }
}
