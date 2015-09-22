<?php
/**
 * @copyright Copyright (C) 2015 AIZAWA Hina
 * @license https://github.com/fetus-hina/fest.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\models;

use Yii;

/**
 * This is the model class for table "mvp".
 *
 * @property integer $id
 * @property integer $data_id
 * @property integer $color_id
 * @property string $name
 *
 * @property Color $color
 * @property OfficialData $data
 */
class Mvp extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'mvp';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['data_id', 'color_id', 'name'], 'required'],
            [['data_id', 'color_id'], 'integer'],
            [['name'], 'string', 'max' => 10]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'data_id' => 'Data ID',
            'color_id' => 'Color ID',
            'name' => 'Name',
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
