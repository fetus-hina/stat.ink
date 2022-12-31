<?php

/**
 * @copyright Copyright (C) 2015-2021 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\models;

/**
 * This is the model class for table "splatfest".
 *
 * @property integer $id
 * @property integer $region_id
 * @property string $name
 * @property string $start_at
 * @property string $end_at
 * @property integer $order
 *
 * @property Region $region
 * @property SplatfestBattleSummary[] $splatfestBattleSummaries
 * @property SplatfestMap[] $splatfestMaps
 * @property SplatfestTeam[] $splatfestTeams
 * @property Team[] $teams
 */
class Splatfest extends \yii\db\ActiveRecord
{
    public static function findCurrentFest()
    {
        $t = gmdate('Y-m-d\TH:i:sP', (int)(@$_SERVER['REQUEST_TIME'] ?: time()));
        return static::find()
            ->innerJoinWith('region', false)
            ->andWhere(['and',
                ['<=', '{{splatfest}}.[[start_at]]', $t],
                ['>',  '{{splatfest}}.[[end_at]]', $t],
            ]);
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'splatfest';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['region_id', 'name', 'start_at', 'end_at', 'order'], 'required'],
            [['region_id', 'order'], 'integer'],
            [['start_at', 'end_at'], 'safe'],
            [['name'], 'string', 'max' => 64],
            [['region_id', 'order'], 'unique', 'targetAttribute' => ['region_id', 'order'],
                'message' => 'The combination of  and Region ID has already been taken.'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'region_id' => 'Region ID',
            'name' => 'Name',
            'start_at' => 'Start At',
            'end_at' => 'End At',
            'order' => 'Order',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRegion()
    {
        return $this->hasOne(Region::class, ['id' => 'region_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSplatfestBattleSummaries()
    {
        return $this->hasMany(SplatfestBattleSummary::class, ['fest_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSplatfestMaps()
    {
        return $this->hasMany(SplatfestMap::class, ['splatfest_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSplatfestTeams()
    {
        return $this->hasMany(SplatfestTeam::class, ['fest_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTeams()
    {
        return $this->hasMany(Team::class, ['id' => 'team_id'])->viaTable('splatfest_team', ['fest_id' => 'id']);
    }
}
