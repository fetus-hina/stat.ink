<?php
/**
 * @copyright Copyright (C) 2015 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\models;

use Yii;
use app\components\helpers\Translator;

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
            [['key'], 'unique']
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
        return $this->hasOne(Ability::className(), ['id' => 'ability_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBrand()
    {
        return $this->hasOne(Brand::className(), ['id' => 'brand_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getType()
    {
        return $this->hasOne(GearType::className(), ['id' => 'type_id']);
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
}
