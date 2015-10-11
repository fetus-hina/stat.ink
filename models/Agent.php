<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "agent".
 *
 * @property integer $id
 * @property string $name
 * @property string $version
 *
 * @property Battle[] $battles
 */
class Agent extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'agent';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'version'], 'required'],
            [['name'], 'string', 'max' => 64],
            [['version'], 'string', 'max' => 255],
            [['name', 'version'], 'unique',
                'targetAttribute' => ['name', 'version'],
                'message' => 'The combination of Name and Version has already been taken.']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'version' => 'Version',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBattles()
    {
        return $this->hasMany(Battle::className(), ['agent_id' => 'id']);
    }
}
