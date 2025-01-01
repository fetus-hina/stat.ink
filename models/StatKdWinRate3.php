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
 * This is the model class for table "stat_kd_win_rate3".
 *
 * @property integer $season_id
 * @property integer $lobby_id
 * @property integer $rule_id
 * @property integer $kills
 * @property integer $deaths
 * @property integer $battles
 * @property integer $wins
 *
 * @property Lobby3 $lobby
 * @property Rule3 $rule
 * @property Season3 $season
 */
class StatKdWinRate3 extends ActiveRecord
{
    public static function tableName()
    {
        return 'stat_kd_win_rate3';
    }

    public function rules()
    {
        return [
            [['season_id', 'lobby_id', 'rule_id', 'kills', 'deaths', 'battles', 'wins'], 'required'],
            [['season_id', 'lobby_id', 'rule_id', 'kills', 'deaths', 'battles', 'wins'], 'default', 'value' => null],
            [['season_id', 'lobby_id', 'rule_id', 'kills', 'deaths', 'battles', 'wins'], 'integer'],
            [['season_id', 'lobby_id', 'rule_id', 'kills', 'deaths'], 'unique', 'targetAttribute' => ['season_id', 'lobby_id', 'rule_id', 'kills', 'deaths']],
            [['lobby_id'], 'exist', 'skipOnError' => true, 'targetClass' => Lobby3::class, 'targetAttribute' => ['lobby_id' => 'id']],
            [['rule_id'], 'exist', 'skipOnError' => true, 'targetClass' => Rule3::class, 'targetAttribute' => ['rule_id' => 'id']],
            [['season_id'], 'exist', 'skipOnError' => true, 'targetClass' => Season3::class, 'targetAttribute' => ['season_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'season_id' => 'Season ID',
            'lobby_id' => 'Lobby ID',
            'rule_id' => 'Rule ID',
            'kills' => 'Kills',
            'deaths' => 'Deaths',
            'battles' => 'Battles',
            'wins' => 'Wins',
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
