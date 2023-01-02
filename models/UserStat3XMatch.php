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
 * This is the model class for table "user_stat3_x_match".
 *
 * @property integer $user_id
 * @property integer $rule_id
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
 * @property string $peak_x_power
 * @property string $peak_season
 * @property string $current_x_power
 * @property string $current_season
 * @property string $updated_at
 *
 * @property Rule3 $rule
 * @property User $user
 */
class UserStat3XMatch extends ActiveRecord
{
    public static function tableName()
    {
        return 'user_stat3_x_match';
    }

    public function rules()
    {
        return [
            [['user_id', 'rule_id', 'battles', 'agg_battles', 'agg_seconds', 'wins', 'kills', 'assists', 'deaths', 'specials', 'inked', 'max_inked', 'updated_at'], 'required'],
            [['user_id', 'rule_id', 'battles', 'agg_battles', 'agg_seconds', 'wins', 'kills', 'assists', 'deaths', 'specials', 'inked', 'max_inked'], 'default', 'value' => null],
            [['user_id', 'rule_id', 'battles', 'agg_battles', 'agg_seconds', 'wins', 'kills', 'assists', 'deaths', 'specials', 'inked', 'max_inked'], 'integer'],
            [['peak_x_power', 'current_x_power'], 'number'],
            [['peak_season', 'current_season', 'updated_at'], 'safe'],
            [['user_id', 'rule_id'], 'unique', 'targetAttribute' => ['user_id', 'rule_id']],
            [['rule_id'], 'exist', 'skipOnError' => true, 'targetClass' => Rule3::class, 'targetAttribute' => ['rule_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'user_id' => 'User ID',
            'rule_id' => 'Rule ID',
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
            'peak_x_power' => 'Peak X Power',
            'peak_season' => 'Peak Season',
            'current_x_power' => 'Current X Power',
            'current_season' => 'Current Season',
            'updated_at' => 'Updated At',
        ];
    }

    public function getRule(): ActiveQuery
    {
        return $this->hasOne(Rule3::class, ['id' => 'rule_id']);
    }

    public function getUser(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }
}
