<?php

/**
 * @copyright Copyright (C) 2022-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

namespace app\models;

use Override;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "lobby3".
 *
 * @property integer $id
 * @property string $key
 * @property string $name
 * @property integer $rank
 * @property integer $group_id
 * @property string $short_name
 *
 * @property Battle3[] $battle3s
 * @property LobbyGroup3 $group
 * @property Schedule3[] $schedule3s
 * @property Splatfest3StatsWeapon[] $splatfest3StatsWeapons
 * @property StatKdWinRate3[] $statKdWinRate3s
 * @property StatMatchingRangeDiff3[] $statMatchingRangeDiff3s
 * @property StatMatchingRangeSpread3[] $statMatchingRangeSpread3s
 * @property StatMatchingRangeSpreadDiff3[] $statMatchingRangeSpreadDiff3s
 * @property StatMatchingRangeTeam3[] $statMatchingRangeTeam3s
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
 * @property UserBadge3SeriesWin[] $userBadge3SeriesWins
 * @property UserStat3[] $userStat3s
 * @property User[] $users
 * @property User[] $users0
 */
class Lobby3 extends ActiveRecord
{
    public static function tableName()
    {
        return 'lobby3';
    }

    #[Override]
    public function rules()
    {
        return [
            [['key', 'name', 'rank', 'group_id', 'short_name'], 'required'],
            [['rank', 'group_id'], 'default', 'value' => null],
            [['rank', 'group_id'], 'integer'],
            [['short_name'], 'string'],
            [['key', 'name'], 'string', 'max' => 32],
            [['key'], 'unique'],
            [['group_id'], 'exist', 'skipOnError' => true, 'targetClass' => LobbyGroup3::class, 'targetAttribute' => ['group_id' => 'id']],
        ];
    }

    #[Override]
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'key' => 'Key',
            'name' => 'Name',
            'rank' => 'Rank',
            'group_id' => 'Group ID',
            'short_name' => 'Short Name',
        ];
    }

    public function getBattle3s(): ActiveQuery
    {
        return $this->hasMany(Battle3::class, ['lobby_id' => 'id']);
    }

    public function getGroup(): ActiveQuery
    {
        return $this->hasOne(LobbyGroup3::class, ['id' => 'group_id']);
    }

    public function getSchedule3s(): ActiveQuery
    {
        return $this->hasMany(Schedule3::class, ['lobby_id' => 'id']);
    }

    public function getSplatfest3StatsWeapons(): ActiveQuery
    {
        return $this->hasMany(Splatfest3StatsWeapon::class, ['lobby_id' => 'id']);
    }

    public function getStatKdWinRate3s(): ActiveQuery
    {
        return $this->hasMany(StatKdWinRate3::class, ['lobby_id' => 'id']);
    }

    public function getStatMatchingRangeDiff3s(): ActiveQuery
    {
        return $this->hasMany(StatMatchingRangeDiff3::class, ['lobby_id' => 'id']);
    }

    public function getStatMatchingRangeSpread3s(): ActiveQuery
    {
        return $this->hasMany(StatMatchingRangeSpread3::class, ['lobby_id' => 'id']);
    }

    public function getStatMatchingRangeSpreadDiff3s(): ActiveQuery
    {
        return $this->hasMany(StatMatchingRangeSpreadDiff3::class, ['lobby_id' => 'id']);
    }

    public function getStatMatchingRangeTeam3s(): ActiveQuery
    {
        return $this->hasMany(StatMatchingRangeTeam3::class, ['lobby_id' => 'id']);
    }

    public function getStatWeapon3AssistPerVersions(): ActiveQuery
    {
        return $this->hasMany(StatWeapon3AssistPerVersion::class, ['lobby_id' => 'id']);
    }

    public function getStatWeapon3Assists(): ActiveQuery
    {
        return $this->hasMany(StatWeapon3Assist::class, ['lobby_id' => 'id']);
    }

    public function getStatWeapon3DeathPerVersions(): ActiveQuery
    {
        return $this->hasMany(StatWeapon3DeathPerVersion::class, ['lobby_id' => 'id']);
    }

    public function getStatWeapon3Deaths(): ActiveQuery
    {
        return $this->hasMany(StatWeapon3Death::class, ['lobby_id' => 'id']);
    }

    public function getStatWeapon3InkedPerVersions(): ActiveQuery
    {
        return $this->hasMany(StatWeapon3InkedPerVersion::class, ['lobby_id' => 'id']);
    }

    public function getStatWeapon3Inkeds(): ActiveQuery
    {
        return $this->hasMany(StatWeapon3Inked::class, ['lobby_id' => 'id']);
    }

    public function getStatWeapon3KillOrAssistPerVersions(): ActiveQuery
    {
        return $this->hasMany(StatWeapon3KillOrAssistPerVersion::class, ['lobby_id' => 'id']);
    }

    public function getStatWeapon3KillOrAssists(): ActiveQuery
    {
        return $this->hasMany(StatWeapon3KillOrAssist::class, ['lobby_id' => 'id']);
    }

    public function getStatWeapon3KillPerVersions(): ActiveQuery
    {
        return $this->hasMany(StatWeapon3KillPerVersion::class, ['lobby_id' => 'id']);
    }

    public function getStatWeapon3Kills(): ActiveQuery
    {
        return $this->hasMany(StatWeapon3Kill::class, ['lobby_id' => 'id']);
    }

    public function getStatWeapon3SpecialPerVersions(): ActiveQuery
    {
        return $this->hasMany(StatWeapon3SpecialPerVersion::class, ['lobby_id' => 'id']);
    }

    public function getStatWeapon3Specials(): ActiveQuery
    {
        return $this->hasMany(StatWeapon3Special::class, ['lobby_id' => 'id']);
    }

    public function getStatWeapon3UsagePerVersions(): ActiveQuery
    {
        return $this->hasMany(StatWeapon3UsagePerVersion::class, ['lobby_id' => 'id']);
    }

    public function getStatWeapon3Usages(): ActiveQuery
    {
        return $this->hasMany(StatWeapon3Usage::class, ['lobby_id' => 'id']);
    }

    public function getUserBadge3SeriesWins(): ActiveQuery
    {
        return $this->hasMany(UserBadge3SeriesWin::class, ['lobby_id' => 'id']);
    }

    public function getUserStat3s(): ActiveQuery
    {
        return $this->hasMany(UserStat3::class, ['lobby_id' => 'id']);
    }

    public function getUsers(): ActiveQuery
    {
        return $this->hasMany(User::class, ['id' => 'user_id'])->viaTable('user_badge3_series_win', ['lobby_id' => 'id']);
    }

    public function getUsers0(): ActiveQuery
    {
        return $this->hasMany(User::class, ['id' => 'user_id'])->viaTable('user_stat3', ['lobby_id' => 'id']);
    }
}
