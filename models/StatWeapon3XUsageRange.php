<?php

/**
 * @copyright Copyright (C) 2023-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "stat_weapon3_x_usage_range".
 *
 * @property integer $id
 * @property integer $term_id
 * @property string $x_power_range
 *
 * @property StatAbility3XUsage[] $statAbility3XUsages
 * @property StatWeapon3XUsagePerVersion[] $statWeapon3XUsagePerVersions
 * @property StatWeapon3XUsage[] $statWeapon3XUsages
 * @property StatWeapon3XUsageTerm $term
 */
class StatWeapon3XUsageRange extends ActiveRecord
{
    public static function tableName()
    {
        return 'stat_weapon3_x_usage_range';
    }

    public function rules()
    {
        return [
            [['term_id', 'x_power_range'], 'required'],
            [['term_id'], 'default', 'value' => null],
            [['term_id'], 'integer'],
            [['x_power_range'], 'string'],
            [['term_id'], 'exist', 'skipOnError' => true, 'targetClass' => StatWeapon3XUsageTerm::class, 'targetAttribute' => ['term_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'term_id' => 'Term ID',
            'x_power_range' => 'X Power Range',
        ];
    }

    public function getStatAbility3XUsages(): ActiveQuery
    {
        return $this->hasMany(StatAbility3XUsage::class, ['range_id' => 'id']);
    }

    public function getStatWeapon3XUsagePerVersions(): ActiveQuery
    {
        return $this->hasMany(StatWeapon3XUsagePerVersion::class, ['range_id' => 'id']);
    }

    public function getStatWeapon3XUsages(): ActiveQuery
    {
        return $this->hasMany(StatWeapon3XUsage::class, ['range_id' => 'id']);
    }

    public function getTerm(): ActiveQuery
    {
        return $this->hasOne(StatWeapon3XUsageTerm::class, ['id' => 'term_id']);
    }
}
