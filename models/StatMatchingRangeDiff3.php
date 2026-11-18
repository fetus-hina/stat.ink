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
 * This is the model class for table "stat_matching_range_diff3".
 *
 * Distribution of the |team MR difference| and wins of the higher-MR team.
 *
 * @property integer $season_id
 * @property integer $lobby_id
 * @property integer $x_power_class
 * @property integer $rule_id
 * @property-read numeric-string $mr_diff
 * @property-write float|string $mr_diff
 * @property integer $battles
 * @property integer|null $higher_wins
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Lobby3 $lobby
 * @property Rule3 $rule
 * @property Season3 $season
 */
class StatMatchingRangeDiff3 extends ActiveRecord
{
    #[Override]
    public static function tableName()
    {
        return 'stat_matching_range_diff3';
    }

    #[Override]
    public function rules()
    {
        return [
            [['season_id', 'lobby_id', 'x_power_class', 'rule_id', 'mr_diff', 'battles', 'created_at', 'updated_at'], 'required'],
            [['season_id', 'lobby_id', 'x_power_class', 'rule_id', 'battles'], 'default', 'value' => null],
            [['season_id', 'lobby_id', 'x_power_class', 'rule_id', 'battles'], 'integer'],
            [['higher_wins'], 'default', 'value' => null],
            [['higher_wins'], 'integer'],
            [['mr_diff'], 'number'],
            [['created_at', 'updated_at'], 'safe'],
            [['season_id', 'lobby_id', 'x_power_class', 'rule_id', 'mr_diff'], 'unique', 'targetAttribute' => ['season_id', 'lobby_id', 'x_power_class', 'rule_id', 'mr_diff']],
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
            'mr_diff' => 'MR Diff',
            'battles' => 'Battles',
            'higher_wins' => 'Higher Wins',
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
