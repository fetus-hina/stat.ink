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
 * This is the model class for table "salmon3_user_stats_played_with".
 *
 * @property integer $user_id
 * @property string $name
 * @property string $number
 * @property integer $jobs
 * @property integer $clear_jobs
 * @property integer $clear_waves
 * @property-read string|null $max_danger_rate_cleared
 * @property-write float|string|null $max_danger_rate_cleared
 * @property-read string|null $max_danger_rate_played
 * @property-write float|string|null $max_danger_rate_played
 * @property double|null $team_golden_egg_avg
 * @property double|null $team_golden_egg_sd
 * @property integer|null $team_golden_egg_max
 * @property integer|null $team_golden_egg_95
 * @property integer|null $team_golden_egg_75
 * @property integer|null $team_golden_egg_50
 * @property integer|null $team_golden_egg_25
 * @property integer|null $team_golden_egg_05
 * @property integer|null $team_golden_egg_min
 * @property double|null $golden_egg_avg
 * @property double|null $golden_egg_sd
 * @property integer|null $golden_egg_max
 * @property integer|null $golden_egg_95
 * @property integer|null $golden_egg_75
 * @property integer|null $golden_egg_50
 * @property integer|null $golden_egg_25
 * @property integer|null $golden_egg_05
 * @property integer|null $golden_egg_min
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
 * @property User $user
 */
class Salmon3UserStatsPlayedWith extends ActiveRecord
{
    public static function tableName()
    {
        return 'salmon3_user_stats_played_with';
    }

    #[Override]
    public function rules()
    {
        return [
            [['max_danger_rate_cleared', 'max_danger_rate_played', 'team_golden_egg_avg', 'team_golden_egg_sd', 'team_golden_egg_max', 'team_golden_egg_95', 'team_golden_egg_75', 'team_golden_egg_50', 'team_golden_egg_25', 'team_golden_egg_05', 'team_golden_egg_min', 'golden_egg_avg', 'golden_egg_sd', 'golden_egg_max', 'golden_egg_95', 'golden_egg_75', 'golden_egg_50', 'golden_egg_25', 'golden_egg_05', 'golden_egg_min', 'rescue_avg', 'rescue_sd', 'rescue_max', 'rescue_95', 'rescue_75', 'rescue_50', 'rescue_25', 'rescue_05', 'rescue_min', 'rescued_avg', 'rescued_sd', 'rescued_max', 'rescued_95', 'rescued_75', 'rescued_50', 'rescued_25', 'rescued_05', 'rescued_min', 'defeat_boss_avg', 'defeat_boss_sd', 'defeat_boss_max', 'defeat_boss_95', 'defeat_boss_75', 'defeat_boss_50', 'defeat_boss_25', 'defeat_boss_05', 'defeat_boss_min'], 'default', 'value' => null],
            [['user_id', 'name', 'number', 'jobs', 'clear_jobs', 'clear_waves'], 'required'],
            [['user_id', 'jobs', 'clear_jobs', 'clear_waves', 'team_golden_egg_max', 'team_golden_egg_95', 'team_golden_egg_75', 'team_golden_egg_50', 'team_golden_egg_25', 'team_golden_egg_05', 'team_golden_egg_min', 'golden_egg_max', 'golden_egg_95', 'golden_egg_75', 'golden_egg_50', 'golden_egg_25', 'golden_egg_05', 'golden_egg_min', 'rescue_max', 'rescue_95', 'rescue_75', 'rescue_50', 'rescue_25', 'rescue_05', 'rescue_min', 'rescued_max', 'rescued_95', 'rescued_75', 'rescued_50', 'rescued_25', 'rescued_05', 'rescued_min', 'defeat_boss_max', 'defeat_boss_95', 'defeat_boss_75', 'defeat_boss_50', 'defeat_boss_25', 'defeat_boss_05', 'defeat_boss_min'], 'default', 'value' => null],
            [['user_id', 'jobs', 'clear_jobs', 'clear_waves', 'team_golden_egg_max', 'team_golden_egg_95', 'team_golden_egg_75', 'team_golden_egg_50', 'team_golden_egg_25', 'team_golden_egg_05', 'team_golden_egg_min', 'golden_egg_max', 'golden_egg_95', 'golden_egg_75', 'golden_egg_50', 'golden_egg_25', 'golden_egg_05', 'golden_egg_min', 'rescue_max', 'rescue_95', 'rescue_75', 'rescue_50', 'rescue_25', 'rescue_05', 'rescue_min', 'rescued_max', 'rescued_95', 'rescued_75', 'rescued_50', 'rescued_25', 'rescued_05', 'rescued_min', 'defeat_boss_max', 'defeat_boss_95', 'defeat_boss_75', 'defeat_boss_50', 'defeat_boss_25', 'defeat_boss_05', 'defeat_boss_min'], 'integer'],
            [['max_danger_rate_cleared', 'max_danger_rate_played', 'team_golden_egg_avg', 'team_golden_egg_sd', 'golden_egg_avg', 'golden_egg_sd', 'rescue_avg', 'rescue_sd', 'rescued_avg', 'rescued_sd', 'defeat_boss_avg', 'defeat_boss_sd'], 'number'],
            [['name', 'number'], 'string', 'max' => 10],
            [['user_id', 'jobs', 'clear_jobs', 'clear_waves', 'max_danger_rate_cleared', 'max_danger_rate_played', 'name', 'number'], 'unique', 'targetAttribute' => ['user_id', 'jobs', 'clear_jobs', 'clear_waves', 'max_danger_rate_cleared', 'max_danger_rate_played', 'name', 'number']],
            [['user_id', 'name', 'number'], 'unique', 'targetAttribute' => ['user_id', 'name', 'number']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    #[Override]
    public function attributeLabels()
    {
        return [
            'user_id' => 'User ID',
            'name' => 'Name',
            'number' => 'Number',
            'jobs' => 'Jobs',
            'clear_jobs' => 'Clear Jobs',
            'clear_waves' => 'Clear Waves',
            'max_danger_rate_cleared' => 'Max Danger Rate Cleared',
            'max_danger_rate_played' => 'Max Danger Rate Played',
            'team_golden_egg_avg' => 'Team Golden Egg Avg',
            'team_golden_egg_sd' => 'Team Golden Egg Sd',
            'team_golden_egg_max' => 'Team Golden Egg Max',
            'team_golden_egg_95' => 'Team Golden Egg 95',
            'team_golden_egg_75' => 'Team Golden Egg 75',
            'team_golden_egg_50' => 'Team Golden Egg 50',
            'team_golden_egg_25' => 'Team Golden Egg 25',
            'team_golden_egg_05' => 'Team Golden Egg 05',
            'team_golden_egg_min' => 'Team Golden Egg Min',
            'golden_egg_avg' => 'Golden Egg Avg',
            'golden_egg_sd' => 'Golden Egg Sd',
            'golden_egg_max' => 'Golden Egg Max',
            'golden_egg_95' => 'Golden Egg 95',
            'golden_egg_75' => 'Golden Egg 75',
            'golden_egg_50' => 'Golden Egg 50',
            'golden_egg_25' => 'Golden Egg 25',
            'golden_egg_05' => 'Golden Egg 05',
            'golden_egg_min' => 'Golden Egg Min',
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

    public function getUser(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }
}
