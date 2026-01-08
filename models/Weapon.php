<?php

/**
 * @copyright Copyright (C) 2015-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\models;

use Yii;
use app\components\helpers\Translator;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

use const SORT_ASC;

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
class Weapon extends ActiveRecord
{
    use SafeFindOneTrait;
    use openapi\Util;

    public static function find()
    {
        return new class (static::class) extends ActiveQuery {
            public function naturalOrder(): ActiveQuery
            {
                return $this->orderBy([
                    'type_id' => SORT_ASC,
                    'key' => SORT_ASC,
                ]);
            }
        };
    }

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
            [['canonical_id', 'main_group_id'], 'required'],
            [['type_id', 'subweapon_id', 'special_id', 'canonical_id', 'main_group_id'], 'integer'],
            [['key', 'name'], 'string', 'max' => 32],
            [['key'], 'unique'],
            [['name'], 'unique'],
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
     * @return ActiveQuery
     */
    public function getBattles()
    {
        return $this->hasMany(Battle::class, ['weapon_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getSpecial()
    {
        return $this->hasOne(Special::class, ['id' => 'special_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getSubweapon()
    {
        return $this->hasOne(Subweapon::class, ['id' => 'subweapon_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getType()
    {
        return $this->hasOne(WeaponType::class, ['id' => 'type_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getUserWeapons()
    {
        return $this->hasMany(UserWeapon::class, ['weapon_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getUsers()
    {
        return $this
            ->hasMany(User::class, ['id' => 'user_id'])
            ->viaTable('user_weapon', ['weapon_id' => 'id']);
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
            'type' => $this->type->toJsonArray(),
            'name' => Translator::translateToAll('app-weapon', $this->name),
            'sub' => $this->subweapon->toJsonArray(),
            'special' => $this->special->toJsonArray(),
        ];
    }

    public static function openApiSchema(): array
    {
        $values = static::find()
            ->naturalOrder()
            ->all();
        return [
            'type' => 'object',
            'description' => Yii::t('app-apidoc1', 'Weapon information'),
            'properties' => [
                'key' => static::oapiKey(
                    static::oapiKeyValueTable(
                        Yii::t('app-apidoc1', 'Weapon'),
                        'app-weapon',
                        $values,
                    ),
                    ArrayHelper::getColumn($values, 'key', false),
                ),
                'name' => static::oapiRef(openapi\Name::class),
                'type' => static::oapiRef(WeaponType::class),
                'sub' => static::oapiRef(Subweapon::class),
                'special' => static::oapiRef(Special::class),
            ],
            'example' => $values[0]->toJsonArray(),
        ];
    }

    public static function openApiDepends(): array
    {
        return [
            Special::class,
            Subweapon::class,
            WeaponType::class,
            openapi\Name::class,
        ];
    }

    public static function openapiExample(): array
    {
        $model = static::find()
            ->where(['key' => 'wakaba'])
            ->limit(1)
            ->one();
        return [
            $model->toJsonArray(),
        ];
    }
}
