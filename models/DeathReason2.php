<?php

/**
 * @copyright Copyright (C) 2017-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\models;

use Yii;
use app\components\helpers\Translator;
use yii\db\ActiveQuery;
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
            [['key'], 'string', 'max' => 32],
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
     * @return ActiveQuery
     */
    public function getType()
    {
        return $this->hasOne(DeathReasonType2::class, ['id' => 'type_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getSpecial()
    {
        return $this->hasOne(Special2::class, ['id' => 'special_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getSubweapon()
    {
        return $this->hasOne(Subweapon2::class, ['id' => 'subweapon_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getWeapon()
    {
        return $this->hasOne(Weapon2::class, ['id' => 'weapon_id']);
    }

    public function toJsonArray()
    {
        return [
            'key' => $this->key,
            'name' => $this->getTranslatedNameList(),
            'type' => $this->type
                ? $this->type->toJsonArray()
                : [
                    'key' => null,
                    'name' => Translator::translateToAll('app-death2', 'Unknown'),
                ],
        ];
    }

    public function getTranslatedNameList()
    {
        if ($this->type) {
            switch ($this->type->key) {
                case 'main':
                    return Translator::translateToAll('app-weapon2', $this->name);

                case 'sub':
                    return Translator::translateToAll('app-subweapon2', $this->name);

                case 'special':
                    return Translator::translateToAll('app-special2', $this->name);
            }
        }
        return Translator::translateToAll('app-death2', $this->name);
    }

    public function getTranslatedName(?string $language = null): string
    {
        if ($this->type) {
            switch ($this->type->key) {
                case 'main':
                    return Yii::t('app-weapon2', $this->name, [], $language);

                case 'sub':
                    return Yii::t('app-subweapon2', $this->name, [], $language);

                case 'special':
                    return Yii::t('app-special2', $this->name, [], $language);
            }
        }
        return Yii::t('app-death2', $this->name, [], $language);
    }
}
