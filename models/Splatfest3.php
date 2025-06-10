<?php

/**
 * @copyright Copyright (C) 2023-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\models;

use Override;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "splatfest3".
 *
 * @property integer $id
 * @property string $key
 * @property string $name
 * @property string $start_at
 * @property string $end_at
 *
 * @property SplatfestCamp3[] $camps
 * @property Splatfest3StatsPower $splatfest3StatsPower
 * @property Splatfest3StatsPowerHistogram[] $splatfest3StatsPowerHistograms
 * @property Splatfest3StatsWeapon[] $splatfest3StatsWeapons
 * @property SplatfestTeam3[] $splatfestTeam3s
 */
class Splatfest3 extends ActiveRecord
{
    public static function tableName()
    {
        return 'splatfest3';
    }

    #[Override]
    public function rules()
    {
        return [
            [['key', 'name', 'start_at', 'end_at'], 'required'],
            [['start_at', 'end_at'], 'safe'],
            [['key'], 'string', 'max' => 32],
            [['name'], 'string', 'max' => 127],
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
            'start_at' => 'Start At',
            'end_at' => 'End At',
        ];
    }

    public function getCamps(): ActiveQuery
    {
        return $this->hasMany(SplatfestCamp3::class, ['id' => 'camp_id'])->viaTable('splatfest_team3', ['fest_id' => 'id']);
    }

    public function getSplatfest3StatsPower(): ActiveQuery
    {
        return $this->hasOne(Splatfest3StatsPower::class, ['splatfest_id' => 'id']);
    }

    public function getSplatfest3StatsPowerHistograms(): ActiveQuery
    {
        return $this->hasMany(Splatfest3StatsPowerHistogram::class, ['splatfest_id' => 'id']);
    }

    public function getSplatfest3StatsWeapons(): ActiveQuery
    {
        return $this->hasMany(Splatfest3StatsWeapon::class, ['fest_id' => 'id']);
    }

    public function getSplatfestTeam3s(): ActiveQuery
    {
        return $this->hasMany(SplatfestTeam3::class, ['fest_id' => 'id']);
    }
}
