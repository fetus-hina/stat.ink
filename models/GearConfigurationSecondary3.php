<?php

/**
 * @copyright Copyright (C) 2022-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "gear_configuration_secondary3".
 *
 * @property integer $id
 * @property integer $config_id
 * @property integer $ability_id
 *
 * @property Ability3 $ability
 * @property GearConfiguration3 $config
 */
class GearConfigurationSecondary3 extends ActiveRecord
{
    public static function tableName()
    {
        return 'gear_configuration_secondary3';
    }

    public function rules()
    {
        return [
            [['config_id'], 'required'],
            [['config_id', 'ability_id'], 'default', 'value' => null],
            [['config_id', 'ability_id'], 'integer'],
            [['ability_id'], 'exist', 'skipOnError' => true, 'targetClass' => Ability3::class, 'targetAttribute' => ['ability_id' => 'id']],
            [['config_id'], 'exist', 'skipOnError' => true, 'targetClass' => GearConfiguration3::class, 'targetAttribute' => ['config_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'config_id' => 'Config ID',
            'ability_id' => 'Ability ID',
        ];
    }

    public function getAbility(): ActiveQuery
    {
        return $this->hasOne(Ability3::class, ['id' => 'ability_id']);
    }

    public function getConfig(): ActiveQuery
    {
        return $this->hasOne(GearConfiguration3::class, ['id' => 'config_id']);
    }
}
