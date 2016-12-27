<?php
/**
 * @copyright Copyright (C) 2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\models;

use Yii;

/**
 * This is the model class for table "stat_weapon_map_trend".
 *
 * @property integer $rule_id
 * @property integer $map_id
 * @property integer $weapon_id
 * @property integer $battles
 *
 * @property Map $map
 * @property Rule $rule
 * @property Weapon $weapon
 */
class StatWeaponMapTrend extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'stat_weapon_map_trend';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['rule_id', 'map_id', 'weapon_id', 'battles'], 'required'],
            [['rule_id', 'map_id', 'weapon_id', 'battles'], 'integer'],
            [['map_id'], 'exist', 'skipOnError' => true,
                'targetClass' => Map::className(),
                'targetAttribute' => ['map_id' => 'id']
            ],
            [['rule_id'], 'exist', 'skipOnError' => true,
                'targetClass' => Rule::className(),
                'targetAttribute' => ['rule_id' => 'id']
            ],
            [['weapon_id'], 'exist', 'skipOnError' => true,
                'targetClass' => Weapon::className(),
                'targetAttribute' => ['weapon_id' => 'id']
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'rule_id' => 'Rule ID',
            'map_id' => 'Map ID',
            'weapon_id' => 'Weapon ID',
            'battles' => 'Battles',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMap()
    {
        return $this->hasOne(Map::className(), ['id' => 'map_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRule()
    {
        return $this->hasOne(Rule::className(), ['id' => 'rule_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getWeapon()
    {
        return $this->hasOne(Weapon::className(), ['id' => 'weapon_id']);
    }
}
