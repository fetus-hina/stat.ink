<?php

/**
 * @copyright Copyright (C) 2016-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "stat_weapon_use_count".
 *
 * @property integer $period
 * @property integer $rule_id
 * @property integer $weapon_id
 * @property integer $battles
 * @property integer $wins
 *
 * @property Rule $rule
 * @property Weapon $weapon
 */
class StatWeaponUseCount extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'stat_weapon_use_count';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['period', 'rule_id', 'weapon_id', 'battles', 'wins'], 'required'],
            [['period', 'rule_id', 'weapon_id', 'battles', 'wins'], 'integer'],
            [['rule_id'], 'exist', 'skipOnError' => true,
                'targetClass' => Rule::class,
                'targetAttribute' => ['rule_id' => 'id'],
            ],
            [['weapon_id'], 'exist', 'skipOnError' => true,
                'targetClass' => Weapon::class,
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
            'period' => 'Period',
            'rule_id' => 'Rule ID',
            'weapon_id' => 'Weapon ID',
            'battles' => 'Battles',
            'wins' => 'Wins',
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getRule()
    {
        return $this->hasOne(Rule::class, ['id' => 'rule_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getWeapon()
    {
        return $this->hasOne(Weapon::class, ['id' => 'weapon_id']);
    }
}
