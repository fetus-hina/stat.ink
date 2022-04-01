<?php

/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\models;

use app\components\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "ostatus_pubsubhubbub".
 *
 * @property int $id
 * @property int $topic
 * @property string $callback
 * @property string $lease_until
 * @property string $secret
 * @property string $created_at
 * @property string $updated_at
 *
 * @property User $topicUser
 */
final class OstatusPubsubhubbub extends ActiveRecord
{
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
     * @return \yii\db\ActiveQuery
     */
    public function getTopicUser()
    {
        return $this->hasOne(User::class, ['id' => 'topic']);
    }

    /**
     * @return null
     */
    public function notify(Battle $battle)
    {
        return null;
    }
}
