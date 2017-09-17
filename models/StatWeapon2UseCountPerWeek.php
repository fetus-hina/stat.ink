<?php
/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "stat_weapon2_use_count_per_week".
 *
 * @property integer $isoyear
 * @property integer $isoweek
 * @property integer $rule_id
 * @property integer $weapon_id
 * @property integer $battles
 * @property integer $wins
 *
 * @property Rule2 $rule
 * @property Weapon2 $weapon
 */
class StatWeapon2UseCountPerWeek extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'stat_weapon2_use_count_per_week';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['isoyear', 'isoweek', 'rule_id', 'weapon_id', 'battles', 'wins'], 'required'],
            [['isoyear', 'isoweek', 'rule_id', 'weapon_id', 'battles', 'wins'], 'integer'],
            [['rule_id'], 'exist', 'skipOnError' => true,
                'targetClass' => Rule2::class,
                'targetAttribute' => ['rule_id' => 'id'],
            ],
            [['weapon_id'], 'exist', 'skipOnError' => true,
                'targetClass' => Weapon2::class,
                'targetAttribute' => ['weapon_id' => 'id'],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'isoyear' => 'Isoyear',
            'isoweek' => 'Isoweek',
            'rule_id' => 'Rule ID',
            'weapon_id' => 'Weapon ID',
            'battles' => 'Battles',
            'wins' => 'Wins',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRule()
    {
        return $this->hasOne(Rule2::class, ['id' => 'rule_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getWeapon()
    {
        return $this->hasOne(Weapon2::class, ['id' => 'weapon_id']);
    }
}
