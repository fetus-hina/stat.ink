<?php

/**
 * @copyright Copyright (C) 2015-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "gear_configuration_secondary".
 *
 * @property integer $id
 * @property integer $config_id
 * @property integer $ability_id
 *
 * @property Ability $ability
 * @property GearConfiguration $config
 */
class GearConfigurationSecondary extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'gear_configuration_secondary';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['config_id'], 'required'],
            [['config_id', 'ability_id'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'config_id' => 'Config ID',
            'ability_id' => 'Ability ID',
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getAbility()
    {
        return $this->hasOne(Ability::class, ['id' => 'ability_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getConfig()
    {
        return $this->hasOne(GearConfiguration::class, ['id' => 'config_id']);
    }

    public function toJsonArray()
    {
        return $this->ability ? $this->ability->toJsonArray() : null;
    }
}
