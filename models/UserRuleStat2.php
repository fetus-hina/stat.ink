<?php
/**
 * @copyright Copyright (C) 2015-2019 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

declare(strict_types=1);

namespace app\models;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "user_rule_stat2".
 *
 * @property integer $user_id
 * @property integer $mode_id
 * @property integer $rule_id
 * @property integer $battles
 * @property integer $have_win_lose
 * @property integer $battles_win
 * @property integer $have_kill
 * @property integer $total_kill
 * @property integer $seconds_kill
 * @property integer $min_kill
 * @property integer $pct5_kill
 * @property integer $q1_4_kill
 * @property integer $median_kill
 * @property integer $q3_4_kill
 * @property integer $pct95_kill
 * @property integer $max_kill
 * @property double $stddev_kill
 * @property integer $have_death
 * @property integer $total_death
 * @property integer $seconds_death
 * @property integer $min_death
 * @property integer $pct5_death
 * @property integer $q1_4_death
 * @property integer $median_death
 * @property integer $q3_4_death
 * @property integer $pct95_death
 * @property integer $max_death
 * @property double $stddev_death
 * @property integer $have_assist
 * @property integer $total_assist
 * @property integer $seconds_assist
 * @property integer $min_assist
 * @property integer $pct5_assist
 * @property integer $q1_4_assist
 * @property integer $median_assist
 * @property integer $q3_4_assist
 * @property integer $pct95_assist
 * @property integer $max_assist
 * @property double $stddev_assist
 * @property integer $have_inked
 * @property integer $total_inked
 * @property integer $seconds_inked
 * @property integer $min_inked
 * @property integer $pct5_inked
 * @property integer $q1_4_inked
 * @property integer $median_inked
 * @property integer $q3_4_inked
 * @property integer $pct95_inked
 * @property integer $max_inked
 * @property double $stddev_inked
 * @property integer $rank_peak
 * @property integer $rank_current
 * @property string $updated_at
 *
 * @property Mode2 $mode
 * @property Rule2 $rule
 * @property User $user
 */
class UserRuleStat2 extends ActiveRecord
{
    public static function tableName()
    {
        return 'user_rule_stat2';
    }

    public function rules()
    {
        return [
            [['user_id', 'mode_id', 'rule_id', 'updated_at'], 'required'],
            [
                [
                    'user_id',
                    'mode_id',
                    'rule_id',
                    'battles',
                    'have_win_lose',
                    'battles_win',
                    'have_kill',
                    'total_kill',
                    'seconds_kill',
                    'min_kill',
                    'pct5_kill',
                    'q1_4_kill',
                    'median_kill',
                    'q3_4_kill',
                    'pct95_kill',
                    'max_kill',
                    'have_death',
                    'total_death',
                    'seconds_death',
                    'min_death',
                    'pct5_death',
                    'q1_4_death',
                    'median_death',
                    'q3_4_death',
                    'pct95_death',
                    'max_death',
                    'have_assist',
                    'total_assist',
                    'seconds_assist',
                    'min_assist',
                    'pct5_assist',
                    'q1_4_assist',
                    'median_assist',
                    'q3_4_assist',
                    'pct95_assist',
                    'max_assist',
                    'have_inked',
                    'total_inked',
                    'seconds_inked',
                    'min_inked',
                    'pct5_inked',
                    'q1_4_inked',
                    'median_inked',
                    'q3_4_inked',
                    'pct95_inked',
                    'max_inked',
                    'rank_peak',
                    'rank_current',
                ],
                'default',
                'value' => null,
            ],
            [
                [
                    'user_id',
                    'mode_id',
                    'rule_id',
                    'battles',
                    'have_win_lose',
                    'battles_win',
                    'have_kill',
                    'total_kill',
                    'seconds_kill',
                    'min_kill',
                    'pct5_kill',
                    'q1_4_kill',
                    'median_kill',
                    'q3_4_kill',
                    'pct95_kill',
                    'max_kill',
                    'have_death',
                    'total_death',
                    'seconds_death',
                    'min_death',
                    'pct5_death',
                    'q1_4_death',
                    'median_death',
                    'q3_4_death',
                    'pct95_death',
                    'max_death',
                    'have_assist',
                    'total_assist',
                    'seconds_assist',
                    'min_assist',
                    'pct5_assist',
                    'q1_4_assist',
                    'median_assist',
                    'q3_4_assist',
                    'pct95_assist',
                    'max_assist',
                    'have_inked',
                    'total_inked',
                    'seconds_inked',
                    'min_inked',
                    'pct5_inked',
                    'q1_4_inked',
                    'median_inked',
                    'q3_4_inked',
                    'pct95_inked',
                    'max_inked',
                    'rank_peak',
                    'rank_current',
                ],
                'integer',
            ],
            [['stddev_kill', 'stddev_death', 'stddev_assist', 'stddev_inked'], 'number'],
            [['updated_at'], 'safe'],
            [['user_id', 'mode_id', 'rule_id'], 'unique',
                'targetAttribute' => [
                    'user_id',
                    'mode_id',
                    'rule_id',
                ],
            ],
            [['mode_id'], 'exist', 'skipOnError' => true,
                'targetClass' => Mode2::class,
                'targetAttribute' => ['mode_id' => 'id'],
            ],
            [['rule_id'], 'exist', 'skipOnError' => true,
                'targetClass' => Rule2::class,
                'targetAttribute' => ['rule_id' => 'id'],
            ],
            [['user_id'], 'exist', 'skipOnError' => true,
                'targetClass' => User::class,
                'targetAttribute' => ['user_id' => 'id'],
            ],
        ];
    }

    public function attributeLabels()
    {
        return [
            'user_id' => 'User ID',
            'mode_id' => 'Mode ID',
            'rule_id' => 'Rule ID',
            'battles' => 'Battles',
            'have_win_lose' => 'Have Win Lose',
            'battles_win' => 'Battles Win',
            'have_kill' => 'Have Kill',
            'total_kill' => 'Total Kill',
            'seconds_kill' => 'Seconds Kill',
            'min_kill' => 'Min Kill',
            'pct5_kill' => 'Pct5 Kill',
            'q1_4_kill' => 'Q1 4 Kill',
            'median_kill' => 'Median Kill',
            'q3_4_kill' => 'Q3 4 Kill',
            'pct95_kill' => 'Pct95 Kill',
            'max_kill' => 'Max Kill',
            'stddev_kill' => 'Stddev Kill',
            'have_death' => 'Have Death',
            'total_death' => 'Total Death',
            'seconds_death' => 'Seconds Death',
            'min_death' => 'Min Death',
            'pct5_death' => 'Pct5 Death',
            'q1_4_death' => 'Q1 4 Death',
            'median_death' => 'Median Death',
            'q3_4_death' => 'Q3 4 Death',
            'pct95_death' => 'Pct95 Death',
            'max_death' => 'Max Death',
            'stddev_death' => 'Stddev Death',
            'have_assist' => 'Have Assist',
            'total_assist' => 'Total Assist',
            'seconds_assist' => 'Seconds Assist',
            'min_assist' => 'Min Assist',
            'pct5_assist' => 'Pct5 Assist',
            'q1_4_assist' => 'Q1 4 Assist',
            'median_assist' => 'Median Assist',
            'q3_4_assist' => 'Q3 4 Assist',
            'pct95_assist' => 'Pct95 Assist',
            'max_assist' => 'Max Assist',
            'stddev_assist' => 'Stddev Assist',
            'have_inked' => 'Have Inked',
            'total_inked' => 'Total Inked',
            'seconds_inked' => 'Seconds Inked',
            'min_inked' => 'Min Inked',
            'pct5_inked' => 'Pct5 Inked',
            'q1_4_inked' => 'Q1 4 Inked',
            'median_inked' => 'Median Inked',
            'q3_4_inked' => 'Q3 4 Inked',
            'pct95_inked' => 'Pct95 Inked',
            'max_inked' => 'Max Inked',
            'stddev_inked' => 'Stddev Inked',
            'rank_peak' => 'Rank Peak',
            'rank_current' => 'Rank Current',
            'updated_at' => 'Updated At',
        ];
    }

    public function getMode(): ActiveQuery
    {
        return $this->hasOne(Mode2::class, ['id' => 'mode_id']);
    }

    public function getRule(): ActiveQuery
    {
        return $this->hasOne(Rule2::class, ['id' => 'rule_id']);
    }

    public function getUser(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }
}
