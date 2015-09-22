<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "official_result".
 *
 * @property integer $fest_id
 * @property integer $alpha_people
 * @property integer $bravo_people
 * @property integer $alpha_win
 * @property integer $bravo_win
 * @property integer $win_rate_times
 *
 * @property Fest $fest
 */
class OfficialResult extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'official_result';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['alpha_people', 'bravo_people', 'alpha_win', 'bravo_win', 'win_rate_times'], 'required'],
            [['alpha_people', 'bravo_people', 'alpha_win', 'bravo_win', 'win_rate_times'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'fest_id' => 'Fest ID',
            'alpha_people' => 'Alpha People',
            'bravo_people' => 'Bravo People',
            'alpha_win' => 'Alpha Win',
            'bravo_win' => 'Bravo Win',
            'win_rate_times' => 'Win Rate Times',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFest()
    {
        return $this->hasOne(Fest::className(), ['id' => 'fest_id']);
    }
}
