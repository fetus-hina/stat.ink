<?php

/**
 * @copyright Copyright (C) 2015-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "splatfest_team".
 *
 * @property integer $fest_id
 * @property integer $team_id
 * @property string $name
 * @property integer $color_hue
 *
 * @property Splatfest $fest
 * @property Team $team
 */
class SplatfestTeam extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'splatfest_team';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['fest_id', 'team_id', 'name'], 'required'],
            [['fest_id', 'team_id', 'color_hue'], 'integer'],
            [['name'], 'string', 'max' => 32],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'fest_id' => 'Fest ID',
            'team_id' => 'Team ID',
            'name' => 'Name',
            'color_hue' => 'Color Hue',
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getFest()
    {
        return $this->hasOne(Splatfest::class, ['id' => 'fest_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getTeam()
    {
        return $this->hasOne(Team::class, ['id' => 'team_id']);
    }
}
