<?php

/**
 * @copyright Copyright (C) 2015-2019 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\models;

use Yii;
use yii\base\InvalidParamException;
use yii\behaviors\AttributeTypecastBehavior;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\helpers\VarDumper;

/**
 * This is the model class for table "battle2_splatnet".
 *
 * @property int $id
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

    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            'typecast' => [
                'class' => AttributeTypecastBehavior::class,
                'attributeTypes' => [
                    'json' => fn ($value): ?object => static::convertToObject($value),
                ],
                'typecastAfterValidate' => true,
                'typecastBeforeSave' => false,
                'typecastAfterSave' => false,
                'typecastAfterFind' => false,
            ],
        ]);
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

    public function validateJson(string $attr, $params): void
    {
        if ($this->hasErrors($attr)) {
            return;
        }

        if (is_object($this->$attr)) {
            return;
        }

        try {
            Json::decode($this->$attr);
        } catch (InvalidParamException $e) {
            $this->addError($attr, Json::$jsonErrorMessages['JSON_ERROR_STATE_MISMATCH']);
        }
    }

    protected static function convertToObject($value): ?object
    {
        $profileId = VarDumper::export($value);
        Yii::beginProfile($profileId, __METHOD__);
        try {
            if (is_object($value)) {
                Yii::trace('value is an object', __METHOD__);
                return $value;
            }

            if (is_array($value) && ArrayHelper::isAssociative($value)) {
                Yii::trace('value is an associative array', __METHOD__);
                return (object)$value;
            }

            if (is_string($value)) {
                Yii::trace('value is a string', __METHOD__);
                Yii::beginProfile('Decode JSON string', __METHOD__);
                try {
                    $value = Json::decode($value, false);
                    if (is_object($value)) {
                        Yii::trace('decoded value is an object', __METHOD__);
                        return $value;
                    } else {
                        Yii::trace('decoded value is NOT an object', __METHOD__);
                    }
                } finally {
                    Yii::endProfile('Decode JSON string', __METHOD__);
                }
            }
        } catch (\Throwable $e) {
            Yii::error('Could not convert to object: ' . $e->getMessage(), __METHOD__);
        } finally {
            Yii::endProfile($profileId, __METHOD__);
        }

        Yii::warning('returns null', __METHOD__);
        return null;
    }
}
