<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "weapon".
 *
 * @property integer $id
 * @property integer $type_id
 * @property string $key
 * @property string $name
 *
 * @property WeaponType $type
 */
class Weapon extends \yii\db\ActiveRecord
{
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
            [['type_id', 'key', 'name'], 'required'],
            [['type_id'], 'integer'],
            [['key'], 'string', 'max' => 32],
            [['name'], 'string', 'max' => 16],
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
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getType()
    {
        return $this->hasOne(WeaponType::className(), ['id' => 'type_id']);
    }
}
