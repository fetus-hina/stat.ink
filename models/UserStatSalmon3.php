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
 * This is the model class for table "user_stat_salmon3".
 *
 * @property integer $user_id
 * @property integer $jobs
 * @property integer $agg_jobs
 * @property integer $clear_jobs
 * @property integer $total_waves
 * @property integer $clear_waves
 * @property integer $king_appearances
 * @property integer $king_defeated
 * @property integer $golden_eggs
 * @property integer $power_eggs
 * @property integer $rescues
 * @property integer $rescued
 * @property string $peak_danger_rate
 * @property integer $peak_title_id
 * @property integer $peak_title_exp
 * @property integer $boss_defeated
 *
 * @property SalmonTitle3 $peakTitle
 * @property User $user
 */
class UserStatSalmon3 extends ActiveRecord
{
    public static function tableName()
    {
        return 'user_stat_salmon3';
    }

    public function rules()
    {
        return [
            [['user_id', 'jobs', 'agg_jobs', 'clear_jobs', 'total_waves', 'clear_waves', 'king_appearances', 'king_defeated', 'golden_eggs', 'power_eggs', 'rescues', 'rescued'], 'required'],
            [['user_id', 'jobs', 'agg_jobs', 'clear_jobs', 'total_waves', 'clear_waves', 'king_appearances', 'king_defeated', 'golden_eggs', 'power_eggs', 'rescues', 'rescued', 'peak_title_id', 'peak_title_exp', 'boss_defeated'], 'default', 'value' => null],
            [['user_id', 'jobs', 'agg_jobs', 'clear_jobs', 'total_waves', 'clear_waves', 'king_appearances', 'king_defeated', 'golden_eggs', 'power_eggs', 'rescues', 'rescued', 'peak_title_id', 'peak_title_exp', 'boss_defeated'], 'integer'],
            [['peak_danger_rate'], 'number'],
            [['user_id'], 'unique'],
            [['peak_title_id'], 'exist', 'skipOnError' => true, 'targetClass' => SalmonTitle3::class, 'targetAttribute' => ['peak_title_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'user_id' => 'User ID',
            'jobs' => 'Jobs',
            'agg_jobs' => 'Agg Jobs',
            'clear_jobs' => 'Clear Jobs',
            'total_waves' => 'Total Waves',
            'clear_waves' => 'Clear Waves',
            'king_appearances' => 'King Appearances',
            'king_defeated' => 'King Defeated',
            'golden_eggs' => 'Golden Eggs',
            'power_eggs' => 'Power Eggs',
            'rescues' => 'Rescues',
            'rescued' => 'Rescued',
            'peak_danger_rate' => 'Peak Danger Rate',
            'peak_title_id' => 'Peak Title ID',
            'peak_title_exp' => 'Peak Title Exp',
            'boss_defeated' => 'Boss Defeated',
        ];
    }

    public function getPeakTitle(): ActiveQuery
    {
        return $this->hasOne(SalmonTitle3::class, ['id' => 'peak_title_id']);
    }

    public function getUser(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }
}
