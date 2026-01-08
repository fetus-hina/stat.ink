<?php

/**
 * @copyright Copyright (C) 2024-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\models;

use Override;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "stat_weapon3_x_usage_per_version".
 *
 * @property integer $version_group_id
 * @property integer $rule_id
 * @property integer $range_id
 * @property integer $weapon_id
 * @property integer $battles
 * @property integer $wins
 * @property integer $seconds
 * @property double $avg_kill
 * @property double $sd_kill
 * @property integer $min_kill
 * @property string $p05_kill
 * @property string $p25_kill
 * @property string $p50_kill
 * @property string $p75_kill
 * @property string $p95_kill
 * @property integer $max_kill
 * @property integer $mode_kill
 * @property double $avg_assist
 * @property double $sd_assist
 * @property integer $min_assist
 * @property string $p05_assist
 * @property string $p25_assist
 * @property string $p50_assist
 * @property string $p75_assist
 * @property string $p95_assist
 * @property integer $max_assist
 * @property integer $mode_assist
 * @property double $avg_death
 * @property double $sd_death
 * @property integer $min_death
 * @property string $p05_death
 * @property string $p25_death
 * @property string $p50_death
 * @property string $p75_death
 * @property string $p95_death
 * @property integer $max_death
 * @property integer $mode_death
 * @property double $avg_special
 * @property double $sd_special
 * @property integer $min_special
 * @property string $p05_special
 * @property string $p25_special
 * @property string $p50_special
 * @property string $p75_special
 * @property string $p95_special
 * @property integer $max_special
 * @property integer $mode_special
 * @property double $avg_inked
 * @property double $sd_inked
 * @property integer $min_inked
 * @property string $p05_inked
 * @property string $p25_inked
 * @property string $p50_inked
 * @property string $p75_inked
 * @property string $p95_inked
 * @property integer $max_inked
 * @property integer $mode_inked
 *
 * @property StatWeapon3XUsageRange $range
 * @property Rule3 $rule
 * @property SplatoonVersionGroup3 $versionGroup
 * @property Weapon3 $weapon
 */
class StatWeapon3XUsagePerVersion extends ActiveRecord
{
    public static function tableName()
    {
        return 'stat_weapon3_x_usage_per_version';
    }

    #[Override]
    public function rules()
    {
        return [
            [['sd_kill', 'p05_kill', 'p25_kill', 'p50_kill', 'p75_kill', 'p95_kill', 'mode_kill', 'sd_assist', 'p05_assist', 'p25_assist', 'p50_assist', 'p75_assist', 'p95_assist', 'mode_assist', 'sd_death', 'p05_death', 'p25_death', 'p50_death', 'p75_death', 'p95_death', 'mode_death', 'sd_special', 'p05_special', 'p25_special', 'p50_special', 'p75_special', 'p95_special', 'mode_special', 'sd_inked', 'p05_inked', 'p25_inked', 'p50_inked', 'p75_inked', 'p95_inked', 'mode_inked'], 'default', 'value' => null],
            [['version_group_id', 'rule_id', 'range_id', 'weapon_id', 'battles', 'wins', 'seconds', 'avg_kill', 'min_kill', 'max_kill', 'avg_assist', 'min_assist', 'max_assist', 'avg_death', 'min_death', 'max_death', 'avg_special', 'min_special', 'max_special', 'avg_inked', 'min_inked', 'max_inked'], 'required'],
            [['version_group_id', 'rule_id', 'range_id', 'weapon_id', 'battles', 'wins', 'seconds', 'min_kill', 'max_kill', 'mode_kill', 'min_assist', 'max_assist', 'mode_assist', 'min_death', 'max_death', 'mode_death', 'min_special', 'max_special', 'mode_special', 'min_inked', 'max_inked', 'mode_inked'], 'default', 'value' => null],
            [['version_group_id', 'rule_id', 'range_id', 'weapon_id', 'battles', 'wins', 'seconds', 'min_kill', 'max_kill', 'mode_kill', 'min_assist', 'max_assist', 'mode_assist', 'min_death', 'max_death', 'mode_death', 'min_special', 'max_special', 'mode_special', 'min_inked', 'max_inked', 'mode_inked'], 'integer'],
            [['avg_kill', 'sd_kill', 'p05_kill', 'p25_kill', 'p50_kill', 'p75_kill', 'p95_kill', 'avg_assist', 'sd_assist', 'p05_assist', 'p25_assist', 'p50_assist', 'p75_assist', 'p95_assist', 'avg_death', 'sd_death', 'p05_death', 'p25_death', 'p50_death', 'p75_death', 'p95_death', 'avg_special', 'sd_special', 'p05_special', 'p25_special', 'p50_special', 'p75_special', 'p95_special', 'avg_inked', 'sd_inked', 'p05_inked', 'p25_inked', 'p50_inked', 'p75_inked', 'p95_inked'], 'number'],
            [['version_group_id', 'rule_id', 'range_id', 'weapon_id'], 'unique', 'targetAttribute' => ['version_group_id', 'rule_id', 'range_id', 'weapon_id']],
            [['rule_id'], 'exist', 'skipOnError' => true, 'targetClass' => Rule3::class, 'targetAttribute' => ['rule_id' => 'id']],
            [['version_group_id'], 'exist', 'skipOnError' => true, 'targetClass' => SplatoonVersionGroup3::class, 'targetAttribute' => ['version_group_id' => 'id']],
            [['range_id'], 'exist', 'skipOnError' => true, 'targetClass' => StatWeapon3XUsageRange::class, 'targetAttribute' => ['range_id' => 'id']],
            [['weapon_id'], 'exist', 'skipOnError' => true, 'targetClass' => Weapon3::class, 'targetAttribute' => ['weapon_id' => 'id']],
        ];
    }

    #[Override]
    public function attributeLabels()
    {
        return [
            'version_group_id' => 'Version Group ID',
            'rule_id' => 'Rule ID',
            'range_id' => 'Range ID',
            'weapon_id' => 'Weapon ID',
            'battles' => 'Battles',
            'wins' => 'Wins',
            'seconds' => 'Seconds',
            'avg_kill' => 'Avg Kill',
            'sd_kill' => 'Sd Kill',
            'min_kill' => 'Min Kill',
            'p05_kill' => 'P05 Kill',
            'p25_kill' => 'P25 Kill',
            'p50_kill' => 'P50 Kill',
            'p75_kill' => 'P75 Kill',
            'p95_kill' => 'P95 Kill',
            'max_kill' => 'Max Kill',
            'mode_kill' => 'Mode Kill',
            'avg_assist' => 'Avg Assist',
            'sd_assist' => 'Sd Assist',
            'min_assist' => 'Min Assist',
            'p05_assist' => 'P05 Assist',
            'p25_assist' => 'P25 Assist',
            'p50_assist' => 'P50 Assist',
            'p75_assist' => 'P75 Assist',
            'p95_assist' => 'P95 Assist',
            'max_assist' => 'Max Assist',
            'mode_assist' => 'Mode Assist',
            'avg_death' => 'Avg Death',
            'sd_death' => 'Sd Death',
            'min_death' => 'Min Death',
            'p05_death' => 'P05 Death',
            'p25_death' => 'P25 Death',
            'p50_death' => 'P50 Death',
            'p75_death' => 'P75 Death',
            'p95_death' => 'P95 Death',
            'max_death' => 'Max Death',
            'mode_death' => 'Mode Death',
            'avg_special' => 'Avg Special',
            'sd_special' => 'Sd Special',
            'min_special' => 'Min Special',
            'p05_special' => 'P05 Special',
            'p25_special' => 'P25 Special',
            'p50_special' => 'P50 Special',
            'p75_special' => 'P75 Special',
            'p95_special' => 'P95 Special',
            'max_special' => 'Max Special',
            'mode_special' => 'Mode Special',
            'avg_inked' => 'Avg Inked',
            'sd_inked' => 'Sd Inked',
            'min_inked' => 'Min Inked',
            'p05_inked' => 'P05 Inked',
            'p25_inked' => 'P25 Inked',
            'p50_inked' => 'P50 Inked',
            'p75_inked' => 'P75 Inked',
            'p95_inked' => 'P95 Inked',
            'max_inked' => 'Max Inked',
            'mode_inked' => 'Mode Inked',
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

    public function getVersionGroup(): ActiveQuery
    {
        return $this->hasOne(SplatoonVersionGroup3::class, ['id' => 'version_group_id']);
    }

    public function getWeapon(): ActiveQuery
    {
        return $this->hasOne(Weapon3::class, ['id' => 'weapon_id']);
    }
}
