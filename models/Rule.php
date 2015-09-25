<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "rule".
 *
 * @property integer $id
 * @property integer $mode_id
 * @property string $key
 * @property string $name
 *
 * @property Battle[] $battles
 * @property GameMode $mode
 */
class Rule extends \yii\db\ActiveRecord
{
    use SafeFindOneTrait;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'rule';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['mode_id', 'key', 'name'], 'required'],
            [['mode_id'], 'integer'],
            [['key'], 'string', 'max' => 16],
            [['name'], 'string', 'max' => 32],
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
            'mode_id' => 'Mode ID',
            'key' => 'Key',
            'name' => 'Name',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBattles()
    {
        return $this->hasMany(Battle::className(), ['rule_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMode()
    {
        return $this->hasOne(GameMode::className(), ['id' => 'mode_id']);
    }

    public function toJsonArray()
    {
        return [
            'key' => $this->key,
            'mode' => [
                'key' => $this->mode->key,
                'name' => [
                    'ja_JP' => $this->mode->name,
                ],
            ],
            'name' => [
                'ja_JP' => $this->name,
            ],
        ];
    }
}
