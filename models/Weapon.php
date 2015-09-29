<?php
namespace app\models;

use Yii;
use app\components\helpers\Translator;

/**
 * This is the model class for table "weapon".
 *
 * @property integer $id
 * @property integer $type_id
 * @property string $key
 * @property string $name
 * @property integer $subweapon_id
 * @property integer $special_id
 *
 * @property Battle[] $battles
 * @property Special $special
 * @property Subweapon $subweapon
 * @property WeaponType $type
 */
class Weapon extends \yii\db\ActiveRecord
{
    use SafeFindOneTrait;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'weapon';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type_id', 'key', 'name', 'subweapon_id', 'special_id'], 'required'],
            [['type_id', 'subweapon_id', 'special_id'], 'integer'],
            [['key', 'name'], 'string', 'max' => 32],
            [['key'], 'unique'],
            [['name'], 'unique']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'type_id' => 'Type ID',
            'key' => 'Key',
            'name' => 'Name',
            'subweapon_id' => 'Subweapon ID',
            'special_id' => 'Special ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBattles()
    {
        return $this->hasMany(Battle::className(), ['weapon_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSpecial()
    {
        return $this->hasOne(Special::className(), ['id' => 'special_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSubweapon()
    {
        return $this->hasOne(Subweapon::className(), ['id' => 'subweapon_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getType()
    {
        return $this->hasOne(WeaponType::className(), ['id' => 'type_id']);
    }

    public function toJsonArray()
    {
        return [
            'key' => $this->key,
            'type' => [
                'key' => $this->type->key,
                'name' => Translator::translateToAll('app-weapon', $this->type->name),
            ],
            'name' => Translator::translateToAll('app-weapon', $this->name),
            'sub' => $this->subweapon->toJsonArray(),
            'special' => $this->special->toJsonArray(),
        ];
    }
}
