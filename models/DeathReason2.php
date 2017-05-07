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
 * This is the model class for table "death_reason2".
 *
 * @property integer $id
 * @property string $key
 * @property integer $type_id
 * @property integer $weapon_id
 * @property integer $subweapon_id
 * @property integer $special_id
 * @property string $name
 *
 * @property DeathReasonType2 $type
 * @property Special2 $special
 * @property Subweapon2 $subweapon
 * @property Weapon2 $weapon
 */
class DeathReason2 extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'death_reason2';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['key', 'name'], 'required'],
            [['type_id', 'weapon_id', 'subweapon_id', 'special_id'], 'integer'],
            [['key'], 'string', 'max' => 16],
            [['name'], 'string', 'max' => 32],
            [['key'], 'unique'],
            [['type_id'], 'exist', 'skipOnError' => true,
                'targetClass' => DeathReasonType2::class,
                'targetAttribute' => ['type_id' => 'id'],
            ],
            [['special_id'], 'exist', 'skipOnError' => true,
                'targetClass' => Special2::class,
                'targetAttribute' => ['special_id' => 'id'],
            ],
            [['subweapon_id'], 'exist', 'skipOnError' => true,
                'targetClass' => Subweapon2::class,
                'targetAttribute' => ['subweapon_id' => 'id'],
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
            'id' => 'ID',
            'key' => 'Key',
            'type_id' => 'Type ID',
            'weapon_id' => 'Weapon ID',
            'subweapon_id' => 'Subweapon ID',
            'special_id' => 'Special ID',
            'name' => 'Name',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getType()
    {
        return $this->hasOne(DeathReasonType2::class, ['id' => 'type_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSpecial()
    {
        return $this->hasOne(Special2::class, ['id' => 'special_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSubweapon()
    {
        return $this->hasOne(Subweapon2::class, ['id' => 'subweapon_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getWeapon()
    {
        return $this->hasOne(Weapon2::class, ['id' => 'weapon_id']);
    }
}
