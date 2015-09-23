<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "battle_image".
 *
 * @property integer $id
 * @property integer $battle_id
 * @property integer $type_id
 * @property string $filename
 *
 * @property Battle $battle
 * @property BattleImageType $type
 */
class BattleImage extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'battle_image';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['battle_id', 'type_id', 'filename'], 'required'],
            [['battle_id', 'type_id'], 'integer'],
            [['filename'], 'string', 'max' => 32]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'battle_id' => 'Battle ID',
            'type_id' => 'Type ID',
            'filename' => 'Filename',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBattle()
    {
        return $this->hasOne(Battle::className(), ['id' => 'battle_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getType()
    {
        return $this->hasOne(BattleImageType::className(), ['id' => 'type_id']);
    }
}
