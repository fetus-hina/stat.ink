<?php

/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\models;

use Curl\Curl;
use Yii;
use app\components\behaviors\TimestampBehavior;
use app\components\helpers\BattleAtom;
use app\models\query\OstatusPubsubhubbubQuery;
use yii\db\ActiveRecord;
use yii\helpers\Url;

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
    public static function find(): OstatusPubsubhubbubQuery
    {
        return new OstatusPubsubhubbubQuery(static::class);
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
     * @return \yii\db\ActiveQuery
     */
    public function getTopicUser()
    {
        return $this->hasOne(User::class, ['id' => 'topic']);
    }

    public function notify(Battle $battle): ?string
    {
        $atom = BattleAtom::createUserFeed($battle->user, [$battle->id]);
        $hash = $this->secret != ''
            ? hash_hmac('sha1', $atom, $this->secret, false)
            : null;

        $curl = new Curl();
        $curl->setHeader('Content-Type', 'application/atom+xml');
        $curl->setHeader('Link', sprintf(
            '<%s>; rel=self',
            Url::to(['/ostatus/feed', 'screen_name' => $battle->user->screen_name], true)
        ));
        if ($hash) {
            $curl->setHeader('X-Hub-Signature', "sha1={$hash}");
        }
        $curl->post($this->callback, $atom);
        if ($curl->error) {
            Yii::error('app.ostatus', sprintf(
                '%s(): post failed, %s',
                __METHOD__,
                $curl->errorMessage
            ));
            return null;
        }
        return $atom;
    }
}
