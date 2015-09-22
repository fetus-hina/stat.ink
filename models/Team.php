<?php
/**
 * @copyright Copyright (C) 2015 AIZAWA Hina
 * @license https://github.com/fetus-hina/fest.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\models;

use Yii;

/**
 * This is the model class for table "team".
 *
 * @property integer $fest_id
 * @property integer $color_id
 * @property string $name
 * @property string $keyword
 * @property string $ink_color
 *
 * @property Color $color
 * @property Fest $fest
 */
class Team extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'team';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['fest_id', 'color_id', 'name', 'keyword'], 'required'],
            [['fest_id', 'color_id'], 'integer'],
            [['name', 'keyword'], 'string'],
            [['ink_color'], 'string', 'max' => 6],
            [['fest_id', 'color_id'], 'unique', 'targetAttribute' => ['fest_id', 'color_id'],
                'message' => 'The combination of Fest ID and Color ID has already been taken.']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'fest_id' => 'Fest ID',
            'color_id' => 'Color ID',
            'name' => 'Name',
            'keyword' => 'Keyword',
            'ink_color' => 'Ink Color',
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
    public function getFest()
    {
        return $this->hasOne(Fest::className(), ['id' => 'fest_id']);
    }
}
