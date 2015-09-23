<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "battle_gachi".
 *
 * @property integer $id
 * @property boolean $is_knock_out
 * @property integer $my_team_count
 * @property integer $his_team_count
 *
 * @property Battle $id0
 */
class BattleGachi extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'battle_gachi';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id'], 'required'],
            [['id', 'my_team_count', 'his_team_count'], 'integer'],
            [['is_knock_out'], 'boolean']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'is_knock_out' => 'Is Knock Out',
            'my_team_count' => 'My Team Count',
            'his_team_count' => 'His Team Count',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getId0()
    {
        return $this->hasOne(Battle::className(), ['id' => 'id']);
    }
}
