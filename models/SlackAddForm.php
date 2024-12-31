<?php

/**
 * @copyright Copyright (C) 2016-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 * @author Dog2puppy <Dog2puppy@users.noreply.github.com>
 */

declare(strict_types=1);

namespace app\models;

use Yii;
use app\components\helpers\db\Now;
use yii\base\Model;

use function implode;
use function is_string;
use function preg_match;
use function preg_quote;
use function sprintf;
use function str_contains;
use function strtolower;
use function trim;
use function vsprintf;

final class SlackAddForm extends Model
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
        $discordError = Yii::t('app', 'Make empty this field when you are using Discord.');

        return [
            [['webhook_url', 'language_id'], 'required'],
            [['username', 'icon', 'channel'], 'filter',
                'filter' => fn ($v) => trim($v) === '' ? null : trim($v),
            ],
            [['webhook_url'], 'url'],
            [['webhook_url'], 'validateWebhookUrl'],

            // Disable these fields when a Discord Webhook URL is specified
            //  cf. https://github.com/fetus-hina/stat.ink/issues/1212
            [['username', 'icon', 'channel'], 'string',
                'length' => 0,
                'message' => $discordError,
                'notEqual' => $discordError,
                'tooLong' => $discordError,
                'tooShort' => $discordError,
                'when' => fn (self $model): bool => str_contains(strtolower((string)$model->webhook_url), '//discord'),
                'whenClient' => '()=>$("#slackaddform-webhook_url").val().toLowerCase().includes("//discord")',
            ],

            [['username'], 'match',
                'pattern' => '/^[a-zA-Z0-9._-]{1,21}$/',
            ],
            [['icon'], 'match', 'pattern' => '/^:[a-zA-Z0-9+._-]+:$/',
                'when' => fn (self $model): bool => is_string($model->icon) && !str_contains($model->icon, '//'),
                'whenClient' => '()=>!$("#slackaddform-icon").val().includes("//")',
            ],
            [['icon'], 'url',
                'when' => fn (self $model): bool => is_string($model->icon) && str_contains($model->icon, '//'),
                'whenClient' => '()=>$("#slackaddform-icon").val().includes("//")',
            ],
            [['channel'], 'match',
                'pattern' => vsprintf('/^%s$/', [
                    implode('|', [
                        '(?:#[a-z0-9_-]{1,21})',
                        '(?:@[a-zA-Z0-9._-]{1,21})',
                    ]),
                ]),
            ],
            [['language_id'], 'exist',
                'targetClass' => Language::class,
                'targetAttribute' => 'id',
            ],
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

        $quote = fn (string $regex): string => preg_quote($regex, '/');
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

    public function save(User $user): bool
    {
        $model = Yii::createObject(Slack::class);
        $model->attributes = $this->attributes;
        $model->user_id = $user->id;
        $model->suspended = false;
        $model->created_at = new Now();
        $model->updated_at = new Now();
        return $model->save();
    }
}
