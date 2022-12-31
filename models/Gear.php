<?php

/**
 * @copyright Copyright (C) 2015-2021 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\models;

use Yii;
use app\components\helpers\Translator;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "gear".
 *
 * @property integer $id
 * @property string $key
 * @property integer $type_id
 * @property integer $brand_id
 * @property string $name
 * @property integer $ability_id
 *
 * @property Ability $ability
 * @property Brand $brand
 * @property GearType $type
 */
class Gear extends \yii\db\ActiveRecord
{
    use openapi\Util;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'gear';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['key', 'type_id', 'brand_id', 'name', 'ability_id'], 'required'],
            [['type_id', 'brand_id', 'ability_id'], 'integer'],
            [['key', 'name'], 'string', 'max' => 32],
            [['key'], 'unique'],
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
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAbility()
    {
        return $this->hasOne(Ability::class, ['id' => 'ability_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBrand()
    {
        return $this->hasOne(Brand::class, ['id' => 'brand_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getType()
    {
        return $this->hasOne(GearType::class, ['id' => 'type_id']);
    }

    public function toJsonArray()
    {
        return [
            'key' => $this->key,
            'type' => $this->type->toJsonArray(),
            'brand' => $this->brand ? $this->brand->toJsonArray() : null,
            'name' => Translator::translateToAll('app-gear', $this->name),
            'primary_ability' => $this->ability ? $this->ability->toJsonArray() : null,
        ];
    }

    public static function openApiSchema(): array
    {
        return [
            'type' => 'object',
            'description' => Yii::t('app-apidoc1', 'Gear information'),
            'properties' => [
                'key' => static::oapiKey(),
                'name' => static::oapiRef(openapi\Name::class),
                'type' => static::oapiRef(GearType::class),
                'brand' => array_merge(Brand::openApiSchema(), [
                    'description' => Yii::t('app-apidoc1', 'Brand information'),
                    'nullable' => true,
                ]),
                'primary_ability' => array_merge(Ability::openApiSchema(), [
                    'description' => Yii::t('app-apidoc1', 'Primary ability information'),
                    'nullable' => true,
                ]),
            ],
            'example' => static::openapiExample(),
        ];
    }

    public static function openApiDepends(): array
    {
        return [
            GearType::class,
            openapi\Name::class,
        ];
    }

    public static function openapiExample(): array
    {
        $model = static::find()
            ->andWhere(['key' => 'basic_tee'])
            ->limit(1)
            ->one();
        return [
            $model->toJsonArray(),
        ];
    }
}
