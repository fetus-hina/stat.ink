<?php
/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\models;

use Yii;
use yii\base\InvalidParamException;
use yii\db\ActiveRecord;
use yii\helpers\Json;

/**
 * This is the model class for table "battle2_splatnet".
 *
 * @property integer $id
 * @property string $json
 *
 * @property Battle2 $battle
 */
class Battle2Splatnet extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'battle2_splatnet';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'json'], 'required'],
            [['id'], 'integer'],
            [['json'], 'validateJson'],
            [['id'], 'exist', 'skipOnError' => true,
                'targetClass' => Battle2::class,
                'targetAttribute' => ['id' => 'id'],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'json' => 'Json',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBattle()
    {
        return $this->hasOne(Battle2::class, ['id' => 'id']);
    }

    public function validateJson(string $attr, $params) : void
    {
        if ($this->hasErrors($attr)) {
            return;
        }

        try {
            Json::decode($this->$attr);
        } catch(InvalidParamException $e) {
            $this->addError($attr, Json::$jsonErrorMessages['JSON_ERROR_STATE_MISMATCH']);
        }
    }
}
