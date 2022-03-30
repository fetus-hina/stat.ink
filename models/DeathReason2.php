<?php

/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\models;

use Yii;
use app\components\helpers\Translator;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "death_reason2".
 *
 * @property int $id
 * @property string $key
 * @property int|null $type_id
 * @property int|null $weapon_id
 * @property int|null $subweapon_id
 * @property int|null $special_id
 * @property string $name
 *
 * @property DeathReasonType2|null $type
 * @property Special2|null $special
 * @property Subweapon2|null $subweapon
 * @property Weapon2|null $weapon
 */
final class DeathReason2 extends ActiveRecord
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
