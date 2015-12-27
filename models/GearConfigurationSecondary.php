<?php
/**
 * @copyright Copyright (C) 2015 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\models;

use Yii;

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
class GearConfigurationSecondary extends \yii\db\ActiveRecord
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
            [['config_id', 'ability_id'], 'integer']
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
     * @return \yii\db\ActiveQuery
     */
    public function getAbility()
    {
        return $this->hasOne(Ability::className(), ['id' => 'ability_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getConfig()
    {
        return $this->hasOne(GearConfiguration::className(), ['id' => 'config_id']);
    }

    public function toJsonArray()
    {
        return $this->ability ? $this->ability->toJsonArray() : null;
    }
}
