<?php

/**
 * @copyright Copyright (C) 2022-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

namespace app\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "ability3".
 *
 * @property integer $id
 * @property string $key
 * @property string $name
 * @property integer $rank
 * @property boolean $primary_only
 * @property string $sendouink
 * @property integer|null $gear_type_id
 *
 * @property GearType|null $gearType
 * @property GearConfiguration3[] $gearConfiguration3s
 * @property GearConfigurationSecondary3[] $gearConfigurationSecondary3s
 */
class Ability3 extends ActiveRecord
{
    public static function tableName()
    {
        return 'ability3';
    }

    public function rules()
    {
        return [
            [['key', 'name', 'rank', 'primary_only', 'sendouink'], 'required'],
            [['rank', 'gear_type_id'], 'default', 'value' => null],
            [['rank', 'gear_type_id'], 'integer'],
            [['primary_only'], 'boolean'],
            [['key', 'name'], 'string', 'max' => 32],
            [['sendouink'], 'string', 'max' => 3],
            [['key'], 'unique'],
            [['rank'], 'unique'],
            [['sendouink'], 'unique'],
            [['gear_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => GearType::class, 'targetAttribute' => ['gear_type_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'key' => 'Key',
            'name' => 'Name',
            'rank' => 'Rank',
            'primary_only' => 'Primary Only',
            'sendouink' => 'Sendouink',
            'gear_type_id' => 'Gear Type ID',
        ];
    }

    /**
     * @return ActiveQuery<GearType>
     */
    public function getGearType(): ActiveQuery
    {
        return $this->hasOne(GearType::class, ['id' => 'gear_type_id']);
    }

    public function getGearConfiguration3s(): ActiveQuery
    {
        return $this->hasMany(GearConfiguration3::class, ['ability_id' => 'id']);
    }

    public function getGearConfigurationSecondary3s(): ActiveQuery
    {
        return $this->hasMany(GearConfigurationSecondary3::class, ['ability_id' => 'id']);
    }
}
