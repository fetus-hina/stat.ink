<?php

/**
 * @copyright Copyright (C) 2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\models;

use Yii;
use yii\base\Model;
use app\components\helpers\db\Now;

class SlackAddForm extends Model
{
    public $webhook_url;
    public $username;
    public $icon;
    public $channel;
    public $language_id;

    public function rules()
    {
        return [
            [['webhook_url', 'language_id'], 'required'],
            [['username', 'icon', 'channel'], 'filter',
                'filter' => function ($v) {
                    return (trim($v) === '') ? null : trim($v);
                },
            ],
            [['webhook_url'], 'url'],
            [['webhook_url'], 'validateWebhookUrl'],
            [['username'], 'match', 'pattern' => '/^[a-zA-Z0-9._-]{1,21}$/'],
            [['icon'], 'match', 'pattern' => '/^:[a-zA-Z0-9+._-]+:$/',
                'when' => function ($model) {
                    return is_string($model->icon) && strpos($model->icon, '//') === false;
                },
                'whenClient' => 'function(){return $("#slackaddform-icon").val().indexOf("//")<0}',
            ],
            [['icon'], 'url',
                'when' => function ($model) {
                    return !(is_string($model->icon) && strpos($model->icon, '//') === false);
                },
                'whenClient' => 'function(){return $("#slackaddform-icon").val().indexOf("//")>=0}',
            ],
            [['channel'], 'match',
                'pattern' => sprintf(
                    '/^%s$/',
                    implode('|', [
                        '(?:#[a-z0-9_-]{1,21})',
                        '(?:@[a-zA-Z0-9._-]{1,21})',
                    ])
                ),
            ],
            [['language_id'], 'exist',
                'targetClass' => Language::class,
                'targetAttribute' => 'id'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'webhook_url'       => Yii::t('app', 'Webhook URL'),
            'username'          => Yii::t('app', 'User Name'),
            'icon'              => Yii::t('app', 'Icon'),
            'channel'           => Yii::t('app', 'Channel'),
            'language_id'       => Yii::t('app', 'Language'),
        ];
    }

    public function validateWebhookUrl($attr, $params)
    {
        if ($this->hasErrors($attr)) {
            return;
        }

        $quote = function (string $regex): string {
            return preg_quote($regex, '/');
        };
        $okUrls = [
            // Slack
            sprintf(
                '/^%s/ui',
                $quote('https://hooks.slack.com/services/')
            ),

            // Discord
            sprintf('/^%s/ui', implode('', [
                $quote('https://discord.com/api/webhooks/'),
                '\d+', // snowflake
                $quote('/'),
                '[0-9A-Za-z_-]+',
                $quote('/slack'),
            ])),
        ];

        foreach ($okUrls as $regex) {
            if (preg_match($regex, $this->$attr)) {
                return;
            }
        }

        $this->addError(
            $attr,
            Yii::t('yii', '{attribute} is not a valid URL.', ['attribute' => $this->getAttributeLabel($attr)])
        );
    }

    public function save(User $user)
    {
        $model = new Slack();
        $model->attributes = $this->attributes;
        $model->user_id = $user->id;
        $model->suspended = false;
        $model->created_at = new Now();
        $model->updated_at = new Now();
        return $model->save();
    }
}
