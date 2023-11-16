<?php

/**
 * @copyright Copyright (C) 2015-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\models;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "salmon3_user_stats_golden_egg".
 *
 * @property integer $user_id
 * @property integer $map_id
 * @property integer $shifts
 * @property double $average_team
 * @property double $stddev_team
 * @property integer $histogram_width_team
 * @property double $average_individual
 * @property double $stddev_individual
 * @property integer $histogram_width_individual
 * @property integer $min_team
 * @property string $q1_team
 * @property string $q2_team
 * @property string $q3_team
 * @property integer $max_team
 * @property integer $mode_team
 * @property integer $min_individual
 * @property string $q1_individual
 * @property string $q2_individual
 * @property string $q3_individual
 * @property integer $max_individual
 * @property integer $mode_individual
 *
 * @property SalmonMap3 $map
 * @property User $user
 */
class Salmon3UserStatsGoldenEgg extends ActiveRecord
{
    public static function tableName()
    {
        return 'salmon3_user_stats_golden_egg';
    }

    public function rules()
    {
        return [
            [['user_id', 'map_id', 'shifts'], 'required'],
            [['user_id', 'map_id', 'shifts', 'histogram_width_team', 'histogram_width_individual', 'min_team', 'max_team', 'mode_team', 'min_individual', 'max_individual', 'mode_individual'], 'default', 'value' => null],
            [['user_id', 'map_id', 'shifts', 'histogram_width_team', 'histogram_width_individual', 'min_team', 'max_team', 'mode_team', 'min_individual', 'max_individual', 'mode_individual'], 'integer'],
            [['average_team', 'stddev_team', 'average_individual', 'stddev_individual', 'q1_team', 'q2_team', 'q3_team', 'q1_individual', 'q2_individual', 'q3_individual'], 'number'],
            [['user_id', 'map_id'], 'unique', 'targetAttribute' => ['user_id', 'map_id']],
            [['map_id'], 'exist', 'skipOnError' => true, 'targetClass' => SalmonMap3::class, 'targetAttribute' => ['map_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'user_id' => 'User ID',
            'map_id' => 'Map ID',
            'shifts' => 'Shifts',
            'average_team' => 'Average Team',
            'stddev_team' => 'Stddev Team',
            'histogram_width_team' => 'Histogram Width Team',
            'average_individual' => 'Average Individual',
            'stddev_individual' => 'Stddev Individual',
            'histogram_width_individual' => 'Histogram Width Individual',
            'min_team' => 'Min Team',
            'q1_team' => 'Q1 Team',
            'q2_team' => 'Q2 Team',
            'q3_team' => 'Q3 Team',
            'max_team' => 'Max Team',
            'mode_team' => 'Mode Team',
            'min_individual' => 'Min Individual',
            'q1_individual' => 'Q1 Individual',
            'q2_individual' => 'Q2 Individual',
            'q3_individual' => 'Q3 Individual',
            'max_individual' => 'Max Individual',
            'mode_individual' => 'Mode Individual',
        ];
    }

    public function getMap(): ActiveQuery
    {
        return $this->hasOne(SalmonMap3::class, ['id' => 'map_id']);
    }

    public function getUser(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }
}
