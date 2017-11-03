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
 * This is the model class for table "gear2".
 *
 * @property integer $id
 * @property string $key
 * @property integer $type_id
 * @property integer $brand_id
 * @property string $name
 * @property integer $ability_id
 * @property integer $splatnet
 *
 * @property Ability2 $ability
 * @property Brand2 $brand
 * @property GearType $type
 */
class Gear2 extends ActiveRecord
{
    private $translatedName;

    /*
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'gear2';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['key', 'type_id', 'brand_id', 'name'], 'required'],
            [['type_id', 'brand_id', 'ability_id', 'splatnet'], 'default', 'value' => null],
            [['type_id', 'brand_id', 'ability_id', 'splatnet'], 'integer'],
            [['key', 'name'], 'string', 'max' => 32],
            [['key'], 'unique'],
            [['splatnet'], 'unique'],
            [['ability_id'], 'exist', 'skipOnError' => true,
                'targetClass' => Ability2::class,
                'targetAttribute' => ['ability_id' => 'id'],
            ],
            [['brand_id'], 'exist', 'skipOnError' => true,
                'targetClass' => Brand2::class,
                'targetAttribute' => ['brand_id' => 'id'],
            ],
            [['type_id'], 'exist', 'skipOnError' => true,
                'targetClass' => GearType::class,
                'targetAttribute' => ['type_id' => 'id'],
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
            'brand_id' => 'Brand ID',
            'name' => 'Name',
            'ability_id' => 'Ability ID',
            'splatnet' => 'Splatnet',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAbility()
    {
        return $this->hasOne(Ability2::class, ['id' => 'ability_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBrand()
    {
        return $this->hasOne(Brand2::class, ['id' => 'brand_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getType()
    {
        return $this->hasOne(GearType::class, ['id' => 'type_id']);
    }

    public function getTranslatedName() : string
    {
        if ($this->translatedName === null) {
            $this->translatedName = Yii::t('app-gear2', $this->name);
        }
        return $this->translatedName;
    }
}
