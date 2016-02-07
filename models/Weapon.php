<?php
/**
 * @copyright Copyright (C) 2015-2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

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
 * @property integer $canonical_id
 * @property integer $main_group_id
 *
 * @property Battle[] $battles
 * @property UserWeapon[] $userWeapons
 * @property User[] $users
 * @property Special $special
 * @property Subweapon $subweapon
 * @property WeaponType $type
 * @property Weapon $canonical
 * @property Weapon $mainReference
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
            [['type_id', 'key', 'name', 'subweapon_id', 'special_id', 'canonical_id', 'main_group_id'], 'required'],
            [['type_id', 'subweapon_id', 'special_id', 'canonical_id', 'main_group_id'], 'integer'],
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
        return $this->hasMany(Battle::class, ['weapon_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSpecial()
    {
        return $this->hasOne(Special::class, ['id' => 'special_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSubweapon()
    {
        return $this->hasOne(Subweapon::class, ['id' => 'subweapon_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getType()
    {
        return $this->hasOne(WeaponType::class, ['id' => 'type_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserWeapons()
    {
        return $this->hasMany(UserWeapon::class, ['weapon_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsers()
    {
        return $this->hasMany(User::class, ['id' => 'user_id'])->viaTable('user_weapon', ['weapon_id' => 'id']);
    }

    public function getCanonical()
    {
        return $this->hasOne(static::class, ['id' => 'canonical_id']);
    }

    public function getMainReference()
    {
        return $this->hasOne(static::class, ['id' => 'main_group_id']);
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
