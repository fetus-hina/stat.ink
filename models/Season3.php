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
 * This is the model class for table "season3".
 *
 * @property integer $id
 * @property string $key
 * @property string $name
 * @property string $start_at
 * @property string $end_at
 * @property string $term
 *
 * @property Knockout3[] $knockout3s
 * @property Rule3[] $rules
 * @property StatKdWinRate3[] $statKdWinRate3s
 * @property StatSpecialUse3[] $statSpecialUse3s
 * @property StatSpecialUseCount3[] $statSpecialUseCount3s
 * @property StatWeapon3Assist[] $statWeapon3Assists
 * @property StatWeapon3Death[] $statWeapon3Deaths
 * @property StatWeapon3Inked[] $statWeapon3Inkeds
 * @property StatWeapon3KillOrAssist[] $statWeapon3KillOrAssists
 * @property StatWeapon3Kill[] $statWeapon3Kills
 * @property StatWeapon3Special[] $statWeapon3Specials
 * @property StatWeapon3Usage[] $statWeapon3Usages
 * @property StatWeapon3XUsage[] $statWeapon3XUsages
 * @property StatXPowerDistribAbstract3[] $statXPowerDistribAbstract3s
 * @property StatXPowerDistribHistogram3[] $statXPowerDistribHistogram3s
 * @property XMatchingGroupVersion3[] $xMatchingGroupVersion3s
 */
class Season3 extends ActiveRecord
{
    public static function tableName()
    {
        return 'season3';
    }

    public function rules()
    {
        return [
            [['key', 'name', 'start_at', 'end_at', 'term'], 'required'],
            [['start_at', 'end_at'], 'safe'],
            [['term'], 'string'],
            [['key'], 'string', 'max' => 32],
            [['name'], 'string', 'max' => 64],
            [['key'], 'unique'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'key' => 'Key',
            'name' => 'Name',
            'start_at' => 'Start At',
            'end_at' => 'End At',
            'term' => 'Term',
        ];
    }

    public function getKnockout3s(): ActiveQuery
    {
        return $this->hasMany(Knockout3::class, ['season_id' => 'id']);
    }

    public function getRules(): ActiveQuery
    {
        return $this->hasMany(Rule3::class, ['id' => 'rule_id'])->viaTable('stat_x_power_distrib_abstract3', ['season_id' => 'id']);
    }

    public function getStatKdWinRate3s(): ActiveQuery
    {
        return $this->hasMany(StatKdWinRate3::class, ['season_id' => 'id']);
    }

    public function getStatSpecialUse3s(): ActiveQuery
    {
        return $this->hasMany(StatSpecialUse3::class, ['season_id' => 'id']);
    }

    public function getStatSpecialUseCount3s(): ActiveQuery
    {
        return $this->hasMany(StatSpecialUseCount3::class, ['season_id' => 'id']);
    }

    public function getStatWeapon3Assists(): ActiveQuery
    {
        return $this->hasMany(StatWeapon3Assist::class, ['season_id' => 'id']);
    }

    public function getStatWeapon3Deaths(): ActiveQuery
    {
        return $this->hasMany(StatWeapon3Death::class, ['season_id' => 'id']);
    }

    public function getStatWeapon3Inkeds(): ActiveQuery
    {
        return $this->hasMany(StatWeapon3Inked::class, ['season_id' => 'id']);
    }

    public function getStatWeapon3KillOrAssists(): ActiveQuery
    {
        return $this->hasMany(StatWeapon3KillOrAssist::class, ['season_id' => 'id']);
    }

    public function getStatWeapon3Kills(): ActiveQuery
    {
        return $this->hasMany(StatWeapon3Kill::class, ['season_id' => 'id']);
    }

    public function getStatWeapon3Specials(): ActiveQuery
    {
        return $this->hasMany(StatWeapon3Special::class, ['season_id' => 'id']);
    }

    public function getStatWeapon3Usages(): ActiveQuery
    {
        return $this->hasMany(StatWeapon3Usage::class, ['season_id' => 'id']);
    }

    public function getStatWeapon3XUsages(): ActiveQuery
    {
        return $this->hasMany(StatWeapon3XUsage::class, ['season_id' => 'id']);
    }

    public function getStatXPowerDistribAbstract3s(): ActiveQuery
    {
        return $this->hasMany(StatXPowerDistribAbstract3::class, ['season_id' => 'id']);
    }

    public function getStatXPowerDistribHistogram3s(): ActiveQuery
    {
        return $this->hasMany(StatXPowerDistribHistogram3::class, ['season_id' => 'id']);
    }

    public function getXMatchingGroupVersion3s(): ActiveQuery
    {
        return $this->hasMany(XMatchingGroupVersion3::class, ['minimum_season_id' => 'id']);
    }
}
