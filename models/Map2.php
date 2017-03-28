<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "map2".
 *
 * @property integer $id
 * @property string $key
 * @property string $name
 * @property string $short_name
 * @property integer $area
 * @property string $release_at
 */
class Map2 extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'map2';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['key', 'name', 'short_name'], 'required'],
            [['area'], 'integer'],
            [['release_at'], 'safe'],
            [['key', 'short_name'], 'string', 'max' => 16],
            [['name'], 'string', 'max' => 32],
            [['key'], 'unique'],
            [['name'], 'unique'],
            [['short_name'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'key' => 'Key',
            'name' => 'Name',
            'short_name' => 'Short Name',
            'area' => 'Area',
            'release_at' => 'Release At',
        ];
    }
}
