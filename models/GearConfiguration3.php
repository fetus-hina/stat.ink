<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "gear_configuration3".
 *
 * @property integer $id
 * @property string $fingerprint
 * @property integer $ability_id
 *
 * @property Ability3 $ability
 * @property BattlePlayer3[] $battlePlayer3s
 * @property BattlePlayer3[] $battlePlayer3s0
 * @property BattlePlayer3[] $battlePlayer3s1
 * @property GearConfigurationSecondary3[] $gearConfigurationSecondary3s
 */
class GearConfiguration3 extends ActiveRecord
{
    public static function tableName()
    {
        return 'gear_configuration3';
    }

    public function rules()
    {
        return [
            [['fingerprint'], 'required'],
            [['fingerprint'], 'string'],
            [['ability_id'], 'default', 'value' => null],
            [['ability_id'], 'integer'],
            [['fingerprint'], 'unique'],
            [['ability_id'], 'exist', 'skipOnError' => true, 'targetClass' => Ability3::class, 'targetAttribute' => ['ability_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'fingerprint' => 'Fingerprint',
            'ability_id' => 'Ability ID',
        ];
    }

    public function getAbility(): ActiveQuery
    {
        return $this->hasOne(Ability3::class, ['id' => 'ability_id']);
    }

    public function getBattlePlayer3s(): ActiveQuery
    {
        return $this->hasMany(BattlePlayer3::class, ['headgear_id' => 'id']);
    }

    public function getBattlePlayer3s0(): ActiveQuery
    {
        return $this->hasMany(BattlePlayer3::class, ['clothing_id' => 'id']);
    }

    public function getBattlePlayer3s1(): ActiveQuery
    {
        return $this->hasMany(BattlePlayer3::class, ['shoes_id' => 'id']);
    }

    public function getGearConfigurationSecondary3s(): ActiveQuery
    {
        return $this->hasMany(GearConfigurationSecondary3::class, ['config_id' => 'id']);
    }
}
