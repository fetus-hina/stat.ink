<?php
/**
 * @copyright Copyright (C) 2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\models;

use Yii;

/**
 * This is the model class for table "slack".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $language_id
 * @property string $webhook_url
 * @property string $username
 * @property string $icon
 * @property string $channel
 * @property boolean $suspended
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Language $language
 * @property User $user
 */
class Slack extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'slack';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'language_id', 'webhook_url', 'created_at', 'updated_at'], 'required'],
            [['user_id', 'language_id'], 'integer'],
            [['suspended'], 'boolean'],
            [['created_at', 'updated_at'], 'safe'],
            [['webhook_url', 'icon'], 'string', 'max' => 256],
            [['username'], 'string', 'max' => 15],
            [['channel'], 'string', 'max' => 22],
            [['language_id'], 'exist', 'skipOnError' => true, 'targetClass' => Language::className(), 'targetAttribute' => ['language_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'language_id' => 'Language ID',
            'webhook_url' => 'Webhook Url',
            'username' => 'Username',
            'icon' => 'Icon',
            'channel' => 'Channel',
            'suspended' => 'Suspended',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLanguage()
    {
        return $this->hasOne(Language::className(), ['id' => 'language_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
}
