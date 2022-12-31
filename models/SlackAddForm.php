<?php

/**
 * @copyright Copyright (C) 2015-2021 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 * @author Dog2puppy <Dog2puppy@users.noreply.github.com>
 */

declare(strict_types=1);

namespace app\models;

use Yii;
use app\components\helpers\db\Now;
use yii\base\Model;

class SlackAddForm extends Model
{
    /** @var string */
    public $webhook_url;
    /** @var string */
    public $username;
    /** @var string */
    public $icon;
    /** @var string */
    public $channel;
    /** @var int */
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
                    ]),
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
            'webhook_url' => Yii::t('app', 'Webhook URL'),
            'username' => Yii::t('app', 'User Name'),
            'icon' => Yii::t('app', 'Icon'),
            'channel' => Yii::t('app', 'Channel'),
            'language_id' => Yii::t('app', 'Language'),
        ];
    }

    /**
     * @param string $attr
     * @param mixed $params
     */
    public function validateWebhookUrl($attr, $params): void
    {
        if ($this->hasErrors($attr)) {
            return;
        }

        $quote = fn(string $regex) => preg_quote($regex, '/');
        $okUrls = [
            // Slack
            sprintf(
                '/^%s/ui',
                $quote('https://hooks.slack.com/services/'),
            ),

            // Discord
            sprintf('/^%s/ui', implode('', [
                $quote('https://'),
                sprintf('(?:%s)', implode('|', [
                    $quote('discord.com'),
                    $quote('discordapp.com'),
                ])),
                $quote('/api/webhooks/'),
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
            Yii::t('yii', '{attribute} is not a valid URL.', [
                'attribute' => $this->getAttributeLabel($attr),
            ]),
        );
    }

    /** @return bool */
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
