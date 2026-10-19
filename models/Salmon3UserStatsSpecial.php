<?php

/**
 * @copyright Copyright (C) 2023-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

namespace app\models;

use Override;
use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "salmon3_user_stats_special".
 *
 * @property integer $user_id
 * @property integer $special_id
 * @property integer $jobs
 * @property integer $jobs_cleared
 * @property double|null $waves_cleared_avg
 * @property double|null $waves_cleared_sd
 * @property integer|null $waves_cleared_max
 * @property integer|null $waves_cleared_95
 * @property integer|null $waves_cleared_75
 * @property integer|null $waves_cleared_50
 * @property integer|null $waves_cleared_25
 * @property integer|null $waves_cleared_05
 * @property integer|null $waves_cleared_min
 * @property double|null $golden_egg_avg
 * @property double|null $golden_egg_sd
 * @property integer|null $golden_egg_max
 * @property integer|null $golden_egg_95
 * @property integer|null $golden_egg_75
 * @property integer|null $golden_egg_50
 * @property integer|null $golden_egg_25
 * @property integer|null $golden_egg_05
 * @property integer|null $golden_egg_min
 * @property double|null $power_egg_avg
 * @property double|null $power_egg_sd
 * @property integer|null $power_egg_max
 * @property integer|null $power_egg_95
 * @property integer|null $power_egg_75
 * @property integer|null $power_egg_50
 * @property integer|null $power_egg_25
 * @property integer|null $power_egg_05
 * @property integer|null $power_egg_min
 * @property double|null $rescue_avg
 * @property double|null $rescue_sd
 * @property integer|null $rescue_max
 * @property integer|null $rescue_95
 * @property integer|null $rescue_75
 * @property integer|null $rescue_50
 * @property integer|null $rescue_25
 * @property integer|null $rescue_05
 * @property integer|null $rescue_min
 * @property double|null $rescued_avg
 * @property double|null $rescued_sd
 * @property integer|null $rescued_max
 * @property integer|null $rescued_95
 * @property integer|null $rescued_75
 * @property integer|null $rescued_50
 * @property integer|null $rescued_25
 * @property integer|null $rescued_05
 * @property integer|null $rescued_min
 * @property double|null $defeat_boss_avg
 * @property double|null $defeat_boss_sd
 * @property integer|null $defeat_boss_max
 * @property integer|null $defeat_boss_95
 * @property integer|null $defeat_boss_75
 * @property integer|null $defeat_boss_50
 * @property integer|null $defeat_boss_25
 * @property integer|null $defeat_boss_05
 * @property integer|null $defeat_boss_min
 *
 * @property Special3 $special
 * @property User $user
 */
class Salmon3UserStatsSpecial extends ActiveRecord
{
    public static function tableName()
    {
        return 'salmon3_user_stats_special';
    }

    #[Override]
    public function rules()
    {
        return [
            [['waves_cleared_avg', 'waves_cleared_sd', 'waves_cleared_max', 'waves_cleared_95', 'waves_cleared_75', 'waves_cleared_50', 'waves_cleared_25', 'waves_cleared_05', 'waves_cleared_min', 'golden_egg_avg', 'golden_egg_sd', 'golden_egg_max', 'golden_egg_95', 'golden_egg_75', 'golden_egg_50', 'golden_egg_25', 'golden_egg_05', 'golden_egg_min', 'power_egg_avg', 'power_egg_sd', 'power_egg_max', 'power_egg_95', 'power_egg_75', 'power_egg_50', 'power_egg_25', 'power_egg_05', 'power_egg_min', 'rescue_avg', 'rescue_sd', 'rescue_max', 'rescue_95', 'rescue_75', 'rescue_50', 'rescue_25', 'rescue_05', 'rescue_min', 'rescued_avg', 'rescued_sd', 'rescued_max', 'rescued_95', 'rescued_75', 'rescued_50', 'rescued_25', 'rescued_05', 'rescued_min', 'defeat_boss_avg', 'defeat_boss_sd', 'defeat_boss_max', 'defeat_boss_95', 'defeat_boss_75', 'defeat_boss_50', 'defeat_boss_25', 'defeat_boss_05', 'defeat_boss_min'], 'default', 'value' => null],
            [['user_id', 'special_id', 'jobs', 'jobs_cleared'], 'required'],
            [['user_id', 'special_id', 'jobs', 'jobs_cleared', 'waves_cleared_max', 'waves_cleared_95', 'waves_cleared_75', 'waves_cleared_50', 'waves_cleared_25', 'waves_cleared_05', 'waves_cleared_min', 'golden_egg_max', 'golden_egg_95', 'golden_egg_75', 'golden_egg_50', 'golden_egg_25', 'golden_egg_05', 'golden_egg_min', 'power_egg_max', 'power_egg_95', 'power_egg_75', 'power_egg_50', 'power_egg_25', 'power_egg_05', 'power_egg_min', 'rescue_max', 'rescue_95', 'rescue_75', 'rescue_50', 'rescue_25', 'rescue_05', 'rescue_min', 'rescued_max', 'rescued_95', 'rescued_75', 'rescued_50', 'rescued_25', 'rescued_05', 'rescued_min', 'defeat_boss_max', 'defeat_boss_95', 'defeat_boss_75', 'defeat_boss_50', 'defeat_boss_25', 'defeat_boss_05', 'defeat_boss_min'], 'default', 'value' => null],
            [['user_id', 'special_id', 'jobs', 'jobs_cleared', 'waves_cleared_max', 'waves_cleared_95', 'waves_cleared_75', 'waves_cleared_50', 'waves_cleared_25', 'waves_cleared_05', 'waves_cleared_min', 'golden_egg_max', 'golden_egg_95', 'golden_egg_75', 'golden_egg_50', 'golden_egg_25', 'golden_egg_05', 'golden_egg_min', 'power_egg_max', 'power_egg_95', 'power_egg_75', 'power_egg_50', 'power_egg_25', 'power_egg_05', 'power_egg_min', 'rescue_max', 'rescue_95', 'rescue_75', 'rescue_50', 'rescue_25', 'rescue_05', 'rescue_min', 'rescued_max', 'rescued_95', 'rescued_75', 'rescued_50', 'rescued_25', 'rescued_05', 'rescued_min', 'defeat_boss_max', 'defeat_boss_95', 'defeat_boss_75', 'defeat_boss_50', 'defeat_boss_25', 'defeat_boss_05', 'defeat_boss_min'], 'integer'],
            [['waves_cleared_avg', 'waves_cleared_sd', 'golden_egg_avg', 'golden_egg_sd', 'power_egg_avg', 'power_egg_sd', 'rescue_avg', 'rescue_sd', 'rescued_avg', 'rescued_sd', 'defeat_boss_avg', 'defeat_boss_sd'], 'number'],
            [['user_id', 'special_id'], 'unique', 'targetAttribute' => ['user_id', 'special_id']],
            [['special_id'], 'exist', 'skipOnError' => true, 'targetClass' => Special3::class, 'targetAttribute' => ['special_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    #[Override]
    public function attributeLabels()
    {
        return [
            'user_id' => 'User ID',
            'special_id' => 'Special ID',
            'jobs' => 'Jobs',
            'jobs_cleared' => 'Jobs Cleared',
            'waves_cleared_avg' => 'Waves Cleared Avg',
            'waves_cleared_sd' => 'Waves Cleared Sd',
            'waves_cleared_max' => 'Waves Cleared Max',
            'waves_cleared_95' => 'Waves Cleared 95',
            'waves_cleared_75' => 'Waves Cleared 75',
            'waves_cleared_50' => 'Waves Cleared 50',
            'waves_cleared_25' => 'Waves Cleared 25',
            'waves_cleared_05' => 'Waves Cleared 05',
            'waves_cleared_min' => 'Waves Cleared Min',
            'golden_egg_avg' => 'Golden Egg Avg',
            'golden_egg_sd' => 'Golden Egg Sd',
            'golden_egg_max' => 'Golden Egg Max',
            'golden_egg_95' => 'Golden Egg 95',
            'golden_egg_75' => 'Golden Egg 75',
            'golden_egg_50' => 'Golden Egg 50',
            'golden_egg_25' => 'Golden Egg 25',
            'golden_egg_05' => 'Golden Egg 05',
            'golden_egg_min' => 'Golden Egg Min',
            'power_egg_avg' => 'Power Egg Avg',
            'power_egg_sd' => 'Power Egg Sd',
            'power_egg_max' => 'Power Egg Max',
            'power_egg_95' => 'Power Egg 95',
            'power_egg_75' => 'Power Egg 75',
            'power_egg_50' => 'Power Egg 50',
            'power_egg_25' => 'Power Egg 25',
            'power_egg_05' => 'Power Egg 05',
            'power_egg_min' => 'Power Egg Min',
            'rescue_avg' => 'Rescue Avg',
            'rescue_sd' => 'Rescue Sd',
            'rescue_max' => 'Rescue Max',
            'rescue_95' => 'Rescue 95',
            'rescue_75' => 'Rescue 75',
            'rescue_50' => 'Rescue 50',
            'rescue_25' => 'Rescue 25',
            'rescue_05' => 'Rescue 05',
            'rescue_min' => 'Rescue Min',
            'rescued_avg' => 'Rescued Avg',
            'rescued_sd' => 'Rescued Sd',
            'rescued_max' => 'Rescued Max',
            'rescued_95' => 'Rescued 95',
            'rescued_75' => 'Rescued 75',
            'rescued_50' => 'Rescued 50',
            'rescued_25' => 'Rescued 25',
            'rescued_05' => 'Rescued 05',
            'rescued_min' => 'Rescued Min',
            'defeat_boss_avg' => 'Defeat Boss Avg',
            'defeat_boss_sd' => 'Defeat Boss Sd',
            'defeat_boss_max' => 'Defeat Boss Max',
            'defeat_boss_95' => 'Defeat Boss 95',
            'defeat_boss_75' => 'Defeat Boss 75',
            'defeat_boss_50' => 'Defeat Boss 50',
            'defeat_boss_25' => 'Defeat Boss 25',
            'defeat_boss_05' => 'Defeat Boss 05',
            'defeat_boss_min' => 'Defeat Boss Min',
        ];
    }

    public function getSpecial(): ActiveQuery
    {
        return $this->hasOne(Special3::class, ['id' => 'special_id']);
    }

    public function getUser(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }
}
