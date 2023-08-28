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
            [['user_id', 'map_id', 'shifts', 'histogram_width_team', 'histogram_width_individual'], 'default', 'value' => null],
            [['user_id', 'map_id', 'shifts', 'histogram_width_team', 'histogram_width_individual'], 'integer'],
            [['average_team', 'stddev_team', 'average_individual', 'stddev_individual'], 'number'],
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
