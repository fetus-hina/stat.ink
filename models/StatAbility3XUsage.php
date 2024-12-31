<?php

/**
 * @copyright Copyright (C) 2024-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "stat_ability3_x_usage".
 *
 * @property integer $season_id
 * @property integer $rule_id
 * @property integer $range_id
 * @property integer $weapon_id
 * @property integer $players
 * @property integer $ink_saver_main_players
 * @property double $ink_saver_main_avg
 * @property integer $ink_saver_sub_players
 * @property double $ink_saver_sub_avg
 * @property integer $ink_recovery_up_players
 * @property double $ink_recovery_up_avg
 * @property integer $run_speed_up_players
 * @property double $run_speed_up_avg
 * @property integer $swim_speed_up_players
 * @property double $swim_speed_up_avg
 * @property integer $special_charge_up_players
 * @property double $special_charge_up_avg
 * @property integer $special_saver_players
 * @property double $special_saver_avg
 * @property integer $special_power_up_players
 * @property double $special_power_up_avg
 * @property integer $quick_respawn_players
 * @property double $quick_respawn_avg
 * @property integer $quick_super_jump_players
 * @property double $quick_super_jump_avg
 * @property integer $sub_power_up_players
 * @property double $sub_power_up_avg
 * @property integer $ink_resistance_up_players
 * @property double $ink_resistance_up_avg
 * @property integer $sub_resistance_up_players
 * @property double $sub_resistance_up_avg
 * @property integer $intensify_action_players
 * @property double $intensify_action_avg
 * @property integer $opening_gambit_players
 * @property double $opening_gambit_avg
 * @property integer $last_ditch_effort_players
 * @property double $last_ditch_effort_avg
 * @property integer $tenacity_players
 * @property double $tenacity_avg
 * @property integer $comeback_players
 * @property double $comeback_avg
 * @property integer $ninja_squid_players
 * @property double $ninja_squid_avg
 * @property integer $haunt_players
 * @property double $haunt_avg
 * @property integer $thermal_ink_players
 * @property double $thermal_ink_avg
 * @property integer $respawn_punisher_players
 * @property double $respawn_punisher_avg
 * @property integer $ability_doubler_players
 * @property double $ability_doubler_avg
 * @property integer $stealth_jump_players
 * @property double $stealth_jump_avg
 * @property integer $object_shredder_players
 * @property double $object_shredder_avg
 * @property integer $drop_roller_players
 * @property double $drop_roller_avg
 *
 * @property StatWeapon3XUsageRange $range
 * @property Rule3 $rule
 * @property Season3 $season
 * @property Weapon3 $weapon
 */
class StatAbility3XUsage extends ActiveRecord
{
    public static function tableName()
    {
        return 'stat_ability3_x_usage';
    }

    public function rules()
    {
        return [
            [['season_id', 'rule_id', 'range_id', 'weapon_id', 'players'], 'required'],
            [['season_id', 'rule_id', 'range_id', 'weapon_id', 'players', 'ink_saver_main_players', 'ink_saver_sub_players', 'ink_recovery_up_players', 'run_speed_up_players', 'swim_speed_up_players', 'special_charge_up_players', 'special_saver_players', 'special_power_up_players', 'quick_respawn_players', 'quick_super_jump_players', 'sub_power_up_players', 'ink_resistance_up_players', 'sub_resistance_up_players', 'intensify_action_players', 'opening_gambit_players', 'last_ditch_effort_players', 'tenacity_players', 'comeback_players', 'ninja_squid_players', 'haunt_players', 'thermal_ink_players', 'respawn_punisher_players', 'ability_doubler_players', 'stealth_jump_players', 'object_shredder_players', 'drop_roller_players'], 'default', 'value' => null],
            [['season_id', 'rule_id', 'range_id', 'weapon_id', 'players', 'ink_saver_main_players', 'ink_saver_sub_players', 'ink_recovery_up_players', 'run_speed_up_players', 'swim_speed_up_players', 'special_charge_up_players', 'special_saver_players', 'special_power_up_players', 'quick_respawn_players', 'quick_super_jump_players', 'sub_power_up_players', 'ink_resistance_up_players', 'sub_resistance_up_players', 'intensify_action_players', 'opening_gambit_players', 'last_ditch_effort_players', 'tenacity_players', 'comeback_players', 'ninja_squid_players', 'haunt_players', 'thermal_ink_players', 'respawn_punisher_players', 'ability_doubler_players', 'stealth_jump_players', 'object_shredder_players', 'drop_roller_players'], 'integer'],
            [['ink_saver_main_avg', 'ink_saver_sub_avg', 'ink_recovery_up_avg', 'run_speed_up_avg', 'swim_speed_up_avg', 'special_charge_up_avg', 'special_saver_avg', 'special_power_up_avg', 'quick_respawn_avg', 'quick_super_jump_avg', 'sub_power_up_avg', 'ink_resistance_up_avg', 'sub_resistance_up_avg', 'intensify_action_avg', 'opening_gambit_avg', 'last_ditch_effort_avg', 'tenacity_avg', 'comeback_avg', 'ninja_squid_avg', 'haunt_avg', 'thermal_ink_avg', 'respawn_punisher_avg', 'ability_doubler_avg', 'stealth_jump_avg', 'object_shredder_avg', 'drop_roller_avg'], 'number'],
            [['season_id', 'rule_id', 'range_id', 'weapon_id'], 'unique', 'targetAttribute' => ['season_id', 'rule_id', 'range_id', 'weapon_id']],
            [['rule_id'], 'exist', 'skipOnError' => true, 'targetClass' => Rule3::class, 'targetAttribute' => ['rule_id' => 'id']],
            [['season_id'], 'exist', 'skipOnError' => true, 'targetClass' => Season3::class, 'targetAttribute' => ['season_id' => 'id']],
            [['range_id'], 'exist', 'skipOnError' => true, 'targetClass' => StatWeapon3XUsageRange::class, 'targetAttribute' => ['range_id' => 'id']],
            [['weapon_id'], 'exist', 'skipOnError' => true, 'targetClass' => Weapon3::class, 'targetAttribute' => ['weapon_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'season_id' => 'Season ID',
            'rule_id' => 'Rule ID',
            'range_id' => 'Range ID',
            'weapon_id' => 'Weapon ID',
            'players' => 'Players',
            'ink_saver_main_players' => 'Ink Saver Main Players',
            'ink_saver_main_avg' => 'Ink Saver Main Avg',
            'ink_saver_sub_players' => 'Ink Saver Sub Players',
            'ink_saver_sub_avg' => 'Ink Saver Sub Avg',
            'ink_recovery_up_players' => 'Ink Recovery Up Players',
            'ink_recovery_up_avg' => 'Ink Recovery Up Avg',
            'run_speed_up_players' => 'Run Speed Up Players',
            'run_speed_up_avg' => 'Run Speed Up Avg',
            'swim_speed_up_players' => 'Swim Speed Up Players',
            'swim_speed_up_avg' => 'Swim Speed Up Avg',
            'special_charge_up_players' => 'Special Charge Up Players',
            'special_charge_up_avg' => 'Special Charge Up Avg',
            'special_saver_players' => 'Special Saver Players',
            'special_saver_avg' => 'Special Saver Avg',
            'special_power_up_players' => 'Special Power Up Players',
            'special_power_up_avg' => 'Special Power Up Avg',
            'quick_respawn_players' => 'Quick Respawn Players',
            'quick_respawn_avg' => 'Quick Respawn Avg',
            'quick_super_jump_players' => 'Quick Super Jump Players',
            'quick_super_jump_avg' => 'Quick Super Jump Avg',
            'sub_power_up_players' => 'Sub Power Up Players',
            'sub_power_up_avg' => 'Sub Power Up Avg',
            'ink_resistance_up_players' => 'Ink Resistance Up Players',
            'ink_resistance_up_avg' => 'Ink Resistance Up Avg',
            'sub_resistance_up_players' => 'Sub Resistance Up Players',
            'sub_resistance_up_avg' => 'Sub Resistance Up Avg',
            'intensify_action_players' => 'Intensify Action Players',
            'intensify_action_avg' => 'Intensify Action Avg',
            'opening_gambit_players' => 'Opening Gambit Players',
            'opening_gambit_avg' => 'Opening Gambit Avg',
            'last_ditch_effort_players' => 'Last Ditch Effort Players',
            'last_ditch_effort_avg' => 'Last Ditch Effort Avg',
            'tenacity_players' => 'Tenacity Players',
            'tenacity_avg' => 'Tenacity Avg',
            'comeback_players' => 'Comeback Players',
            'comeback_avg' => 'Comeback Avg',
            'ninja_squid_players' => 'Ninja Squid Players',
            'ninja_squid_avg' => 'Ninja Squid Avg',
            'haunt_players' => 'Haunt Players',
            'haunt_avg' => 'Haunt Avg',
            'thermal_ink_players' => 'Thermal Ink Players',
            'thermal_ink_avg' => 'Thermal Ink Avg',
            'respawn_punisher_players' => 'Respawn Punisher Players',
            'respawn_punisher_avg' => 'Respawn Punisher Avg',
            'ability_doubler_players' => 'Ability Doubler Players',
            'ability_doubler_avg' => 'Ability Doubler Avg',
            'stealth_jump_players' => 'Stealth Jump Players',
            'stealth_jump_avg' => 'Stealth Jump Avg',
            'object_shredder_players' => 'Object Shredder Players',
            'object_shredder_avg' => 'Object Shredder Avg',
            'drop_roller_players' => 'Drop Roller Players',
            'drop_roller_avg' => 'Drop Roller Avg',
        ];
    }

    public function getRange(): ActiveQuery
    {
        return $this->hasOne(StatWeapon3XUsageRange::class, ['id' => 'range_id']);
    }

    public function getRule(): ActiveQuery
    {
        return $this->hasOne(Rule3::class, ['id' => 'rule_id']);
    }

    public function getSeason(): ActiveQuery
    {
        return $this->hasOne(Season3::class, ['id' => 'season_id']);
    }

    public function getWeapon(): ActiveQuery
    {
        return $this->hasOne(Weapon3::class, ['id' => 'weapon_id']);
    }
}
