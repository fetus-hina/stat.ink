<?php

/**
 * @copyright Copyright (C) 2022-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "user_stat3".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $lobby_id
 * @property integer $battles
 * @property integer $agg_battles
 * @property integer $agg_seconds
 * @property integer $wins
 * @property integer $kills
 * @property integer $assists
 * @property integer $deaths
 * @property integer $specials
 * @property integer $inked
 * @property integer $max_inked
 * @property integer $peak_rank_id
 * @property integer $peak_s_plus
 * @property string $peak_x_power
 * @property string $peak_fest_power
 * @property string $peak_season
 * @property integer $current_rank_id
 * @property integer $current_s_plus
 * @property string $current_x_power
 * @property string $current_season
 * @property string $updated_at
 *
 * @property Rank3 $currentRank
 * @property Lobby3 $lobby
 * @property Rank3 $peakRank
 * @property User $user
 */
class UserStat3 extends ActiveRecord
{
    public static function tableName()
    {
        return 'user_stat3';
    }

    public function rules()
    {
        return [
            [['id', 'user_id', 'battles', 'agg_battles', 'agg_seconds', 'wins', 'kills', 'assists', 'deaths', 'specials', 'inked', 'max_inked', 'updated_at'], 'required'],
            [['id', 'user_id', 'lobby_id', 'battles', 'agg_battles', 'agg_seconds', 'wins', 'kills', 'assists', 'deaths', 'specials', 'inked', 'max_inked', 'peak_rank_id', 'peak_s_plus', 'current_rank_id', 'current_s_plus'], 'default', 'value' => null],
            [['id', 'user_id', 'lobby_id', 'battles', 'agg_battles', 'agg_seconds', 'wins', 'kills', 'assists', 'deaths', 'specials', 'inked', 'max_inked', 'peak_rank_id', 'peak_s_plus', 'current_rank_id', 'current_s_plus'], 'integer'],
            [['peak_x_power', 'peak_fest_power', 'current_x_power'], 'number'],
            [['peak_season', 'current_season', 'updated_at'], 'safe'],
            [['user_id', 'lobby_id'], 'unique', 'targetAttribute' => ['user_id', 'lobby_id']],
            [['id'], 'unique'],
            [['lobby_id'], 'exist', 'skipOnError' => true, 'targetClass' => Lobby3::class, 'targetAttribute' => ['lobby_id' => 'id']],
            [['peak_rank_id'], 'exist', 'skipOnError' => true, 'targetClass' => Rank3::class, 'targetAttribute' => ['peak_rank_id' => 'id']],
            [['current_rank_id'], 'exist', 'skipOnError' => true, 'targetClass' => Rank3::class, 'targetAttribute' => ['current_rank_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'lobby_id' => 'Lobby ID',
            'battles' => 'Battles',
            'agg_battles' => 'Agg Battles',
            'agg_seconds' => 'Agg Seconds',
            'wins' => 'Wins',
            'kills' => 'Kills',
            'assists' => 'Assists',
            'deaths' => 'Deaths',
            'specials' => 'Specials',
            'inked' => 'Inked',
            'max_inked' => 'Max Inked',
            'peak_rank_id' => 'Peak Rank ID',
            'peak_s_plus' => 'Peak S Plus',
            'peak_x_power' => 'Peak X Power',
            'peak_fest_power' => 'Peak Fest Power',
            'peak_season' => 'Peak Season',
            'current_rank_id' => 'Current Rank ID',
            'current_s_plus' => 'Current S Plus',
            'current_x_power' => 'Current X Power',
            'current_season' => 'Current Season',
            'updated_at' => 'Updated At',
        ];
    }

    public function getCurrentRank(): ActiveQuery
    {
        return $this->hasOne(Rank3::class, ['id' => 'current_rank_id']);
    }

    public function getLobby(): ActiveQuery
    {
        return $this->hasOne(Lobby3::class, ['id' => 'lobby_id']);
    }

    public function getPeakRank(): ActiveQuery
    {
        return $this->hasOne(Rank3::class, ['id' => 'peak_rank_id']);
    }

    public function getUser(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }
}
