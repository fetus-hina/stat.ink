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
 * This is the model class for table "user_stat_gachi2".
 *
 * @property integer $user_id
 * @property integer $battles
 * @property integer $win_ko
 * @property integer $lose_ko
 * @property integer $win_time
 * @property integer $lose_time
 * @property integer $win_unk
 * @property integer $lose_unk
 * @property integer $have_kill
 * @property integer $total_kill
 * @property integer $total_kill_with_time
 * @property integer $total_time_kill
 * @property integer $min_kill
 * @property double $pct5_kill
 * @property double $q1_4_kill
 * @property double $median_kill
 * @property double $q3_4_kill
 * @property double $pct95_kill
 * @property integer $max_kill
 * @property double $stddev_kill
 * @property integer $have_death
 * @property integer $total_death
 * @property integer $total_death_with_time
 * @property integer $total_time_death
 * @property integer $min_death
 * @property double $pct5_death
 * @property double $q1_4_death
 * @property double $median_death
 * @property double $q3_4_death
 * @property double $pct95_death
 * @property integer $max_death
 * @property double $stddev_death
 * @property integer $have_assist
 * @property integer $total_assist
 * @property integer $total_assist_with_time
 * @property integer $total_time_assist
 * @property integer $min_assist
 * @property double $pct5_assist
 * @property double $q1_4_assist
 * @property double $median_assist
 * @property double $q3_4_assist
 * @property double $pct95_assist
 * @property integer $max_assist
 * @property double $stddev_assist
 * @property string $updated_at
 *
 * @property User $user
 */
class UserStatGachi2 extends ActiveRecord
{
    public static function tableName()
    {
        return 'user_stat_gachi2';
    }

    public function rules()
    {
        return [
            [['user_id', 'updated_at'], 'required'],
            [
                [
                    'user_id',
                    'battles',
                    'win_ko',
                    'lose_ko',
                    'win_time',
                    'lose_time',
                    'win_unk',
                    'lose_unk',
                    'have_kill',
                    'total_kill',
                    'total_kill_with_time',
                    'total_time_kill',
                    'min_kill',
                    'max_kill',
                    'have_death',
                    'total_death',
                    'total_death_with_time',
                    'total_time_death',
                    'min_death',
                    'max_death',
                    'have_assist',
                    'total_assist',
                    'total_assist_with_time',
                    'total_time_assist',
                    'min_assist',
                    'max_assist',
                ],
                'default',
                'value' => null,
            ],
            [
                [
                    'user_id',
                    'battles',
                    'win_ko',
                    'lose_ko',
                    'win_time',
                    'lose_time',
                    'win_unk',
                    'lose_unk',
                    'have_kill',
                    'total_kill',
                    'total_kill_with_time',
                    'total_time_kill',
                    'min_kill',
                    'max_kill',
                    'have_death',
                    'total_death',
                    'total_death_with_time',
                    'total_time_death',
                    'min_death',
                    'max_death',
                    'have_assist',
                    'total_assist',
                    'total_assist_with_time',
                    'total_time_assist',
                    'min_assist',
                    'max_assist',
                ],
                'integer',
            ],
            [
                [
                    'pct5_kill',
                    'q1_4_kill',
                    'median_kill',
                    'q3_4_kill',
                    'pct95_kill',
                    'stddev_kill',
                    'pct5_death',
                    'q1_4_death',
                    'median_death',
                    'q3_4_death',
                    'pct95_death',
                    'stddev_death',
                    'pct5_assist',
                    'q1_4_assist',
                    'median_assist',
                    'q3_4_assist',
                    'pct95_assist',
                    'stddev_assist',
                ],
                'number',
            ],
            [['updated_at'], 'safe'],
            [['user_id'], 'unique'],
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
            'battles' => 'Battles',
            'win_ko' => 'Win Ko',
            'lose_ko' => 'Lose Ko',
            'win_time' => 'Win Time',
            'lose_time' => 'Lose Time',
            'win_unk' => 'Win Unk',
            'lose_unk' => 'Lose Unk',
            'have_kill' => 'Have Kill',
            'total_kill' => 'Total Kill',
            'total_kill_with_time' => 'Total Kill With Time',
            'total_time_kill' => 'Total Time Kill',
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
            'total_death_with_time' => 'Total Death With Time',
            'total_time_death' => 'Total Time Death',
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
            'total_assist_with_time' => 'Total Assist With Time',
            'total_time_assist' => 'Total Time Assist',
            'min_assist' => 'Min Assist',
            'pct5_assist' => 'Pct5 Assist',
            'q1_4_assist' => 'Q1 4 Assist',
            'median_assist' => 'Median Assist',
            'q3_4_assist' => 'Q3 4 Assist',
            'pct95_assist' => 'Pct95 Assist',
            'max_assist' => 'Max Assist',
            'stddev_assist' => 'Stddev Assist',
            'updated_at' => 'Updated At',
        ];
    }

    public function getUser(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }
}
