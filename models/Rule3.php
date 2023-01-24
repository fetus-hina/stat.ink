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
 * This is the model class for table "rule3".
 *
 * @property integer $id
 * @property string $key
 * @property string $name
 * @property string $short_name
 * @property integer $rank
 * @property integer $group_id
 *
 * @property Battle3[] $battle3s
 * @property RuleGroup3 $group
 * @property Knockout3[] $knockout3s
 * @property Rule3Alias[] $rule3Aliases
 * @property Schedule3[] $schedule3s
 * @property Season3[] $seasons
 * @property StatKdWinRate3[] $statKdWinRate3s
 * @property StatSpecialUse3[] $statSpecialUse3s
 * @property StatSpecialUseCount3[] $statSpecialUseCount3s
 * @property StatWeapon3AssistPerVersion[] $statWeapon3AssistPerVersions
 * @property StatWeapon3Assist[] $statWeapon3Assists
 * @property StatWeapon3DeathPerVersion[] $statWeapon3DeathPerVersions
 * @property StatWeapon3Death[] $statWeapon3Deaths
 * @property StatWeapon3InkedPerVersion[] $statWeapon3InkedPerVersions
 * @property StatWeapon3Inked[] $statWeapon3Inkeds
 * @property StatWeapon3KillOrAssistPerVersion[] $statWeapon3KillOrAssistPerVersions
 * @property StatWeapon3KillOrAssist[] $statWeapon3KillOrAssists
 * @property StatWeapon3KillPerVersion[] $statWeapon3KillPerVersions
 * @property StatWeapon3Kill[] $statWeapon3Kills
 * @property StatWeapon3SpecialPerVersion[] $statWeapon3SpecialPerVersions
 * @property StatWeapon3Special[] $statWeapon3Specials
 * @property StatWeapon3UsagePerVersion[] $statWeapon3UsagePerVersions
 * @property StatWeapon3Usage[] $statWeapon3Usages
 * @property StatXPowerDistrib3[] $statXPowerDistrib3s
 * @property StatXPowerDistribAbstract3[] $statXPowerDistribAbstract3s
 * @property UserStat3XMatch[] $userStat3XMatches
 * @property User[] $users
 */
class Rule3 extends ActiveRecord
{
    public static function tableName()
    {
        return 'rule3';
    }

    public function rules()
    {
        return [
            [['key', 'name', 'short_name', 'rank', 'group_id'], 'required'],
            [['rank', 'group_id'], 'default', 'value' => null],
            [['rank', 'group_id'], 'integer'],
            [['key', 'name', 'short_name'], 'string', 'max' => 32],
            [['key'], 'unique'],
            [['group_id'], 'exist', 'skipOnError' => true, 'targetClass' => RuleGroup3::class, 'targetAttribute' => ['group_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'key' => 'Key',
            'name' => 'Name',
            'short_name' => 'Short Name',
            'rank' => 'Rank',
            'group_id' => 'Group ID',
        ];
    }

    public function getBattle3s(): ActiveQuery
    {
        return $this->hasMany(Battle3::class, ['rule_id' => 'id']);
    }

    public function getGroup(): ActiveQuery
    {
        return $this->hasOne(RuleGroup3::class, ['id' => 'group_id']);
    }

    public function getKnockout3s(): ActiveQuery
    {
        return $this->hasMany(Knockout3::class, ['rule_id' => 'id']);
    }

    public function getRule3Aliases(): ActiveQuery
    {
        return $this->hasMany(Rule3Alias::class, ['rule_id' => 'id']);
    }

    public function getSchedule3s(): ActiveQuery
    {
        return $this->hasMany(Schedule3::class, ['rule_id' => 'id']);
    }

    public function getSeasons(): ActiveQuery
    {
        return $this->hasMany(Season3::class, ['id' => 'season_id'])->viaTable('stat_x_power_distrib_abstract3', ['rule_id' => 'id']);
    }

    public function getStatKdWinRate3s(): ActiveQuery
    {
        return $this->hasMany(StatKdWinRate3::class, ['rule_id' => 'id']);
    }

    public function getStatSpecialUse3s(): ActiveQuery
    {
        return $this->hasMany(StatSpecialUse3::class, ['rule_id' => 'id']);
    }

    public function getStatSpecialUseCount3s(): ActiveQuery
    {
        return $this->hasMany(StatSpecialUseCount3::class, ['rule_id' => 'id']);
    }

    public function getStatWeapon3AssistPerVersions(): ActiveQuery
    {
        return $this->hasMany(StatWeapon3AssistPerVersion::class, ['rule_id' => 'id']);
    }

    public function getStatWeapon3Assists(): ActiveQuery
    {
        return $this->hasMany(StatWeapon3Assist::class, ['rule_id' => 'id']);
    }

    public function getStatWeapon3DeathPerVersions(): ActiveQuery
    {
        return $this->hasMany(StatWeapon3DeathPerVersion::class, ['rule_id' => 'id']);
    }

    public function getStatWeapon3Deaths(): ActiveQuery
    {
        return $this->hasMany(StatWeapon3Death::class, ['rule_id' => 'id']);
    }

    public function getStatWeapon3InkedPerVersions(): ActiveQuery
    {
        return $this->hasMany(StatWeapon3InkedPerVersion::class, ['rule_id' => 'id']);
    }

    public function getStatWeapon3Inkeds(): ActiveQuery
    {
        return $this->hasMany(StatWeapon3Inked::class, ['rule_id' => 'id']);
    }

    public function getStatWeapon3KillOrAssistPerVersions(): ActiveQuery
    {
        return $this->hasMany(StatWeapon3KillOrAssistPerVersion::class, ['rule_id' => 'id']);
    }

    public function getStatWeapon3KillOrAssists(): ActiveQuery
    {
        return $this->hasMany(StatWeapon3KillOrAssist::class, ['rule_id' => 'id']);
    }

    public function getStatWeapon3KillPerVersions(): ActiveQuery
    {
        return $this->hasMany(StatWeapon3KillPerVersion::class, ['rule_id' => 'id']);
    }

    public function getStatWeapon3Kills(): ActiveQuery
    {
        return $this->hasMany(StatWeapon3Kill::class, ['rule_id' => 'id']);
    }

    public function getStatWeapon3SpecialPerVersions(): ActiveQuery
    {
        return $this->hasMany(StatWeapon3SpecialPerVersion::class, ['rule_id' => 'id']);
    }

    public function getStatWeapon3Specials(): ActiveQuery
    {
        return $this->hasMany(StatWeapon3Special::class, ['rule_id' => 'id']);
    }

    public function getStatWeapon3UsagePerVersions(): ActiveQuery
    {
        return $this->hasMany(StatWeapon3UsagePerVersion::class, ['rule_id' => 'id']);
    }

    public function getStatWeapon3Usages(): ActiveQuery
    {
        return $this->hasMany(StatWeapon3Usage::class, ['rule_id' => 'id']);
    }

    public function getStatXPowerDistrib3s(): ActiveQuery
    {
        return $this->hasMany(StatXPowerDistrib3::class, ['rule_id' => 'id']);
    }

    public function getStatXPowerDistribAbstract3s(): ActiveQuery
    {
        return $this->hasMany(StatXPowerDistribAbstract3::class, ['rule_id' => 'id']);
    }

    public function getUserStat3XMatches(): ActiveQuery
    {
        return $this->hasMany(UserStat3XMatch::class, ['rule_id' => 'id']);
    }

    public function getUsers(): ActiveQuery
    {
        return $this->hasMany(User::class, ['id' => 'user_id'])->viaTable('user_stat3_x_match', ['rule_id' => 'id']);
    }
}
