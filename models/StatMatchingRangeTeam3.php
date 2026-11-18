<?php

/**
 * @copyright Copyright (C) 2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

namespace app\models;

use Override;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "stat_matching_range_team3".
 *
 * Distribution of the team MR (average of the 4 players).
 *
 * @property integer $season_id
 * @property integer $lobby_id
 * @property integer $x_power_class
 * @property integer $rule_id
 * @property-read numeric-string $mr
 * @property-write float|string $mr
 * @property integer $teams
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Lobby3 $lobby
 * @property Rule3 $rule
 * @property Season3 $season
 */
class StatMatchingRangeTeam3 extends ActiveRecord
{
    #[Override]
    public static function tableName()
    {
        return 'stat_matching_range_team3';
    }

    #[Override]
    public function rules()
    {
        return [
            [['season_id', 'lobby_id', 'x_power_class', 'rule_id', 'mr', 'teams', 'created_at', 'updated_at'], 'required'],
            [['season_id', 'lobby_id', 'x_power_class', 'rule_id', 'teams'], 'default', 'value' => null],
            [['season_id', 'lobby_id', 'x_power_class', 'rule_id', 'teams'], 'integer'],
            [['mr'], 'number'],
            [['created_at', 'updated_at'], 'safe'],
            [['season_id', 'lobby_id', 'x_power_class', 'rule_id', 'mr'], 'unique', 'targetAttribute' => ['season_id', 'lobby_id', 'x_power_class', 'rule_id', 'mr']],
            [['lobby_id'], 'exist', 'skipOnError' => true, 'targetClass' => Lobby3::class, 'targetAttribute' => ['lobby_id' => 'id']],
            [['rule_id'], 'exist', 'skipOnError' => true, 'targetClass' => Rule3::class, 'targetAttribute' => ['rule_id' => 'id']],
            [['season_id'], 'exist', 'skipOnError' => true, 'targetClass' => Season3::class, 'targetAttribute' => ['season_id' => 'id']],
        ];
    }

    #[Override]
    public function attributeLabels()
    {
        return [
            'season_id' => 'Season ID',
            'lobby_id' => 'Lobby ID',
            'x_power_class' => 'X Power Class',
            'rule_id' => 'Rule ID',
            'mr' => 'MR',
            'teams' => 'Teams',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public function getLobby(): ActiveQuery
    {
        return $this->hasOne(Lobby3::class, ['id' => 'lobby_id']);
    }

    public function getRule(): ActiveQuery
    {
        return $this->hasOne(Rule3::class, ['id' => 'rule_id']);
    }

    public function getSeason(): ActiveQuery
    {
        return $this->hasOne(Season3::class, ['id' => 'season_id']);
    }
}
