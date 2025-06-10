<?php

/**
 * @copyright Copyright (C) 2022-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\models;

use Override;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "weapon3".
 *
 * @property integer $id
 * @property string $key
 * @property integer $mainweapon_id
 * @property integer $subweapon_id
 * @property integer $special_id
 * @property integer $canonical_id
 * @property string $name
 * @property string $release_at
 *
 * @property Battle3[] $battle3s
 * @property BattlePlayer3[] $battlePlayer3s
 * @property BattleTricolorPlayer3[] $battleTricolorPlayer3s
 * @property Weapon3 $canonical
 * @property Event3StatsWeapon[] $event3StatsWeapons
 * @property Mainweapon3 $mainweapon
 * @property SalmonWeapon3 $salmonWeapon3
 * @property EventSchedule3[] $schedules
 * @property Special3 $special
 * @property Splatfest3StatsWeapon[] $splatfest3StatsWeapons
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
 * @property StatWeapon3XUsagePerVersion[] $statWeapon3XUsagePerVersions
 * @property StatWeapon3XUsage[] $statWeapon3XUsages
 * @property Subweapon3 $subweapon
 * @property UserWeapon3[] $userWeapon3s
 * @property User[] $users
 * @property XMatchingGroupVersion3[] $versions
 * @property Weapon3Alias[] $weapon3Aliases
 * @property Weapon3[] $weapon3s
 * @property XMatchingGroupWeapon3[] $xMatchingGroupWeapon3s
 */
class Weapon3 extends ActiveRecord
{
    public static function tableName()
    {
        return 'weapon3';
    }

    #[Override]
    public function rules()
    {
        return [
            [['subweapon_id', 'special_id'], 'default', 'value' => null],
            [['canonical_id'], 'default', 'value' => 0],
            [['release_at'], 'default', 'value' => '2022-01-01 09:00:00+09'],
            [['key', 'mainweapon_id', 'name'], 'required'],
            [['mainweapon_id', 'subweapon_id', 'special_id', 'canonical_id'], 'default', 'value' => null],
            [['mainweapon_id', 'subweapon_id', 'special_id', 'canonical_id'], 'integer'],
            [['release_at'], 'safe'],
            [['key'], 'string', 'max' => 32],
            [['name'], 'string', 'max' => 48],
            [['key'], 'unique'],
            [['name'], 'unique'],
            [['mainweapon_id'], 'exist', 'skipOnError' => true, 'targetClass' => Mainweapon3::class, 'targetAttribute' => ['mainweapon_id' => 'id']],
            [['special_id'], 'exist', 'skipOnError' => true, 'targetClass' => Special3::class, 'targetAttribute' => ['special_id' => 'id']],
            [['subweapon_id'], 'exist', 'skipOnError' => true, 'targetClass' => Subweapon3::class, 'targetAttribute' => ['subweapon_id' => 'id']],
            [['canonical_id'], 'exist', 'skipOnError' => true, 'targetClass' => self::class, 'targetAttribute' => ['canonical_id' => 'id']],
        ];
    }

    #[Override]
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'key' => 'Key',
            'mainweapon_id' => 'Mainweapon ID',
            'subweapon_id' => 'Subweapon ID',
            'special_id' => 'Special ID',
            'canonical_id' => 'Canonical ID',
            'name' => 'Name',
            'release_at' => 'Release At',
        ];
    }

    public function getBattle3s(): ActiveQuery
    {
        return $this->hasMany(Battle3::class, ['weapon_id' => 'id']);
    }

    public function getBattlePlayer3s(): ActiveQuery
    {
        return $this->hasMany(BattlePlayer3::class, ['weapon_id' => 'id']);
    }

    public function getBattleTricolorPlayer3s(): ActiveQuery
    {
        return $this->hasMany(BattleTricolorPlayer3::class, ['weapon_id' => 'id']);
    }

    public function getCanonical(): ActiveQuery
    {
        return $this->hasOne(self::class, ['id' => 'canonical_id']);
    }

    public function getEvent3StatsWeapons(): ActiveQuery
    {
        return $this->hasMany(Event3StatsWeapon::class, ['weapon_id' => 'id']);
    }

    public function getMainweapon(): ActiveQuery
    {
        return $this->hasOne(Mainweapon3::class, ['id' => 'mainweapon_id']);
    }

    public function getSalmonWeapon3(): ActiveQuery
    {
        return $this->hasOne(SalmonWeapon3::class, ['weapon_id' => 'id']);
    }

    public function getSchedules(): ActiveQuery
    {
        return $this->hasMany(EventSchedule3::class, ['id' => 'schedule_id'])->viaTable('event3_stats_weapon', ['weapon_id' => 'id']);
    }

    public function getSpecial(): ActiveQuery
    {
        return $this->hasOne(Special3::class, ['id' => 'special_id']);
    }

    public function getSplatfest3StatsWeapons(): ActiveQuery
    {
        return $this->hasMany(Splatfest3StatsWeapon::class, ['weapon_id' => 'id']);
    }

    public function getStatWeapon3AssistPerVersions(): ActiveQuery
    {
        return $this->hasMany(StatWeapon3AssistPerVersion::class, ['weapon_id' => 'id']);
    }

    public function getStatWeapon3Assists(): ActiveQuery
    {
        return $this->hasMany(StatWeapon3Assist::class, ['weapon_id' => 'id']);
    }

    public function getStatWeapon3DeathPerVersions(): ActiveQuery
    {
        return $this->hasMany(StatWeapon3DeathPerVersion::class, ['weapon_id' => 'id']);
    }

    public function getStatWeapon3Deaths(): ActiveQuery
    {
        return $this->hasMany(StatWeapon3Death::class, ['weapon_id' => 'id']);
    }

    public function getStatWeapon3InkedPerVersions(): ActiveQuery
    {
        return $this->hasMany(StatWeapon3InkedPerVersion::class, ['weapon_id' => 'id']);
    }

    public function getStatWeapon3Inkeds(): ActiveQuery
    {
        return $this->hasMany(StatWeapon3Inked::class, ['weapon_id' => 'id']);
    }

    public function getStatWeapon3KillOrAssistPerVersions(): ActiveQuery
    {
        return $this->hasMany(StatWeapon3KillOrAssistPerVersion::class, ['weapon_id' => 'id']);
    }

    public function getStatWeapon3KillOrAssists(): ActiveQuery
    {
        return $this->hasMany(StatWeapon3KillOrAssist::class, ['weapon_id' => 'id']);
    }

    public function getStatWeapon3KillPerVersions(): ActiveQuery
    {
        return $this->hasMany(StatWeapon3KillPerVersion::class, ['weapon_id' => 'id']);
    }

    public function getStatWeapon3Kills(): ActiveQuery
    {
        return $this->hasMany(StatWeapon3Kill::class, ['weapon_id' => 'id']);
    }

    public function getStatWeapon3SpecialPerVersions(): ActiveQuery
    {
        return $this->hasMany(StatWeapon3SpecialPerVersion::class, ['weapon_id' => 'id']);
    }

    public function getStatWeapon3Specials(): ActiveQuery
    {
        return $this->hasMany(StatWeapon3Special::class, ['weapon_id' => 'id']);
    }

    public function getStatWeapon3UsagePerVersions(): ActiveQuery
    {
        return $this->hasMany(StatWeapon3UsagePerVersion::class, ['weapon_id' => 'id']);
    }

    public function getStatWeapon3Usages(): ActiveQuery
    {
        return $this->hasMany(StatWeapon3Usage::class, ['weapon_id' => 'id']);
    }

    public function getStatWeapon3XUsagePerVersions(): ActiveQuery
    {
        return $this->hasMany(StatWeapon3XUsagePerVersion::class, ['weapon_id' => 'id']);
    }

    public function getStatWeapon3XUsages(): ActiveQuery
    {
        return $this->hasMany(StatWeapon3XUsage::class, ['weapon_id' => 'id']);
    }

    public function getSubweapon(): ActiveQuery
    {
        return $this->hasOne(Subweapon3::class, ['id' => 'subweapon_id']);
    }

    public function getUserWeapon3s(): ActiveQuery
    {
        return $this->hasMany(UserWeapon3::class, ['weapon_id' => 'id']);
    }

    public function getUsers(): ActiveQuery
    {
        return $this->hasMany(User::class, ['id' => 'user_id'])->viaTable('user_weapon3', ['weapon_id' => 'id']);
    }

    public function getVersions(): ActiveQuery
    {
        return $this->hasMany(XMatchingGroupVersion3::class, ['id' => 'version_id'])->viaTable('x_matching_group_weapon3', ['weapon_id' => 'id']);
    }

    public function getWeapon3Aliases(): ActiveQuery
    {
        return $this->hasMany(Weapon3Alias::class, ['weapon_id' => 'id']);
    }

    public function getWeapon3s(): ActiveQuery
    {
        return $this->hasMany(self::class, ['canonical_id' => 'id']);
    }

    public function getXMatchingGroupWeapon3s(): ActiveQuery
    {
        return $this->hasMany(XMatchingGroupWeapon3::class, ['weapon_id' => 'id']);
    }
}
