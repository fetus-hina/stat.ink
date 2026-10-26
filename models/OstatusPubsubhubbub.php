<?php

/**
 * @copyright Copyright (C) 2017-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

namespace app\models;

use app\components\behaviors\TimestampBehavior;
use app\models\query\OstatusPubsubhubbubQuery;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "ostatus_pubsubhubbub".
 *
 * @property integer $id
 * @property integer $topic
 * @property string $callback
 * @property string|null $lease_until
 * @property string|null $secret
 * @property string $created_at
 * @property string $updated_at
 *
 * @property User $topicUser
 */
class OstatusPubsubhubbub extends ActiveRecord
{
    public static function find(): OstatusPubsubhubbubQuery
    {
        $query = new OstatusPubsubhubbubQuery(static::class);
        $query->init();
        return $query;
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'ostatus_pubsubhubbub';
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['topic', 'callback'], 'required'],
            [['topic'], 'integer'],
            [['lease_until', 'created_at', 'updated_at'], 'safe'],
            [['callback'], 'string', 'max' => 255],
            [['secret'], 'string', 'max' => 200],
            [['topic'], 'exist', 'skipOnError' => true,
                'targetClass' => User::class,
                'targetAttribute' => ['topic' => 'id'],
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
            'topic' => 'Topic',
            'callback' => 'Callback',
            'lease_until' => 'Lease Until',
            'secret' => 'Secret',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getTopicUser()
    {
        return $this->hasOne(User::class, ['id' => 'topic']);
    }

    public function notify(Battle $battle): ?string
    {
        return null;
    }
}
