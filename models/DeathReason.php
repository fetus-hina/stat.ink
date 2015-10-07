<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "death_reason".
 *
 * @property integer $id
 * @property integer $type_id
 * @property string $key
 * @property string $name
 *
 * @property BattleDeathReason[] $battleDeathReasons
 * @property Battle[] $battles
 * @property DeathReasonType $type
 */
class DeathReason extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'death_reason';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type_id'], 'integer'],
            [['key', 'name'], 'required'],
            [['key'], 'string', 'max' => 32],
            [['name'], 'string', 'max' => 64],
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
    public function getBattleDeathReasons()
    {
        return $this->hasMany(BattleDeathReason::className(), ['reason_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBattles()
    {
        return $this->hasMany(Battle::className(), ['id' => 'battle_id'])->viaTable('battle_death_reason', ['reason_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getType()
    {
        return $this->hasOne(DeathReasonType::className(), ['id' => 'type_id']);
    }
}
