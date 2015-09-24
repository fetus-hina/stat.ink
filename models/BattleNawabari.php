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
 * @property string $my_team_final_percent
 * @property string $his_team_final_percent
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
            [['id', 'my_point', 'my_team_final_point', 'his_team_final_point'], 'integer'],
            [['my_team_final_percent', 'his_team_final_percent'], 'number']
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
            'my_team_final_percent' => 'My Team Final Percent',
            'his_team_final_percent' => 'His Team Final Percent',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getId0()
    {
        return $this->hasOne(Battle::className(), ['id' => 'id']);
    }

    public function getIsMeaningful()
    {
        $props = [
            'my_point',
            'my_team_final_point',
            'his_team_final_point',
            'my_team_final_percent',
            'his_team_final_percent',
        ];
        foreach ($props as $prop) {
            if ($this->$prop !== null) {
                return true;
            }
        }
        return true;
    }
}
