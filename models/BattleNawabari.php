<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "battle_nawabari".
 *
 * @property integer $id
 * @property integer $my_point
 * @property integer $my_team_final_point
 * @property integer $his_team_final_point
 *
 * @property Battle $id0
 */
class BattleNawabari extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'battle_nawabari';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id'], 'required'],
            [['id', 'my_point', 'my_team_final_point', 'his_team_final_point'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'my_point' => 'My Point',
            'my_team_final_point' => 'My Team Final Point',
            'his_team_final_point' => 'His Team Final Point',
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
