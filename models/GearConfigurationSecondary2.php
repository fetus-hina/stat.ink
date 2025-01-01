<?php

/**
 * @copyright Copyright (C) 2017-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

use const SORT_ASC;

/**
 * This is the model class for table "gear_configuration_secondary2".
 *
 * @property integer $id
 * @property integer $config_id
 * @property integer $ability_id
 *
 * @property GearConfiguration2 $config
 * @property GearConfiguration2 $ability
 */
class GearConfigurationSecondary2 extends ActiveRecord
{
    public static function find()
    {
        return parent::find()->orderBy(['gear_configuration_secondary2.config_id' => SORT_ASC]);
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'gear_configuration_secondary2';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['config_id'], 'required'],
            [['config_id', 'ability_id'], 'default', 'value' => null],
            [['config_id', 'ability_id'], 'integer'],
            [['config_id'], 'exist', 'skipOnError' => true,
                'targetClass' => GearConfiguration2::class,
                'targetAttribute' => ['config_id' => 'id'],
            ],
            [['ability_id'], 'exist', 'skipOnError' => true,
                'targetClass' => Ability2::class,
                'targetAttribute' => ['ability_id' => 'id'],
            ],
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
    public function getConfig()
    {
        return $this->hasOne(GearConfiguration2::class, ['id' => 'config_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getAbility()
    {
        return $this->hasOne(Ability2::class, ['id' => 'ability_id']);
    }

    public function toJsonArray()
    {
        return $this->ability ? $this->ability->toJsonArray() : null;
    }
}
