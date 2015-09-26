<?php

namespace app\models;

use Yii;
use app\components\helpers\Translator;

/**
 * This is the model class for table "map".
 *
 * @property integer $id
 * @property string $key
 * @property string $name
 *
 * @property Battle[] $battles
 */
class Map extends \yii\db\ActiveRecord
{
    use SafeFindOneTrait;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'map';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['key', 'name'], 'required'],
            [['key', 'name'], 'string', 'max' => 16],
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
            'key' => 'Key',
            'name' => 'Name',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBattles()
    {
        return $this->hasMany(Battle::className(), ['map_id' => 'id']);
    }

    public function toJsonArray()
    {
        return [
            'key' => $this->key,
            'name' => Translator::translateToAll('app-map', $this->name),
        ];
    }
}
