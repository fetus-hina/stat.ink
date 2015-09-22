<?php
/**
 * @copyright Copyright (C) 2015 AIZAWA Hina
 * @license https://github.com/fetus-hina/fest.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\models;

use Yii;

/**
 * This is the model class for table "official_win_data".
 *
 * @property integer $data_id
 * @property integer $color_id
 * @property integer $count
 *
 * @property Color $color
 * @property OfficialData $data
 */
class OfficialWinData extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'official_win_data';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['data_id', 'color_id', 'count'], 'required'],
            [['data_id', 'color_id', 'count'], 'integer'],
            [['data_id', 'color_id'], 'unique', 'targetAttribute' => ['data_id', 'color_id'],
                'message' => 'The combination of Data ID and Color ID has already been taken.']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'data_id' => 'Data ID',
            'color_id' => 'Color ID',
            'count' => 'Count',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getColor()
    {
        return $this->hasOne(Color::className(), ['id' => 'color_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getData()
    {
        return $this->hasOne(OfficialData::className(), ['id' => 'data_id']);
    }
}
