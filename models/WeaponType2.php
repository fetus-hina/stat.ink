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
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "weapon_type2".
 *
 * @property integer $id
 * @property string $key
 * @property integer $category_id
 * @property string $name
 *
 * @property Weapon2[] $weapon2s
 * @property WeaponCategory2 $category
 */
class WeaponType2 extends ActiveRecord
{
    use openapi\Util;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'weapon_type2';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['key', 'category_id', 'name'], 'required'],
            [['category_id'], 'integer'],
            [['key'], 'string', 'max' => 16],
            [['name'], 'string', 'max' => 32],
            [['key'], 'unique'],
            [['name'], 'unique'],
            [['category_id'], 'exist', 'skipOnError' => true,
                'targetClass' => WeaponCategory2::class,
                'targetAttribute' => ['category_id' => 'id'],
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
            'category_id' => 'Category ID',
            'name' => 'Name',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getWeapons()
    {
        return $this->hasMany(Weapon2::class, ['type_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getWeapon2s()
    {
        return $this->getWeapons();
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCategory()
    {
        return $this->hasOne(WeaponCategory2::class, ['id' => 'category_id']);
    }

    public function toJsonArray(): array
    {
        return [
            'key' => $this->key,
            'name' => Translator::translateToAll('app-weapon2', $this->name),
            'category' => $this->category->toJsonArray(),
        ];
    }

    public static function openApiSchema(): array
    {
        $values = static::find()
            ->orderBy([
                'category_id' => SORT_ASC,
                'rank' => SORT_ASC,
            ])
            ->all();

        return [
            'type' => 'object',
            'description' => Yii::t('app-apidoc2', 'Weapon category information'),
            'properties' => [
                'key' => static::oapiKey(
                    static::oapiKeyValueTable(
                        Yii::t('app-apidoc2', 'Weapon category'),
                        'app-weapon2',
                        $values,
                    ),
                    ArrayHelper::getColumn($values, 'key', false),
                ),
                'name' => static::oapiRef(openapi\Name::class),
                'category' => static::oapiRef(WeaponCategory2::class),
            ],
            'example' => $values[0]->toJsonArray(),
        ];
    }

    public static function openApiDepends(): array
    {
        return [
            WeaponCategory2::class,
            openapi\Name::class,
        ];
    }

    public static function openapiExample(): array
    {
        return array_map(
            function (self $model): array {
                return $model->toJsonArray();
            },
            static::find()
                ->orderBy(['id' => SORT_ASC])
                ->limit(1)
                ->all(),
        );
    }
}
