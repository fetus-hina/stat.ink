<?php

/**
 * @copyright Copyright (C) 2016-2021 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 * @author YDKK <YDKK@users.noreply.github.com>
 */

namespace app\models;

use Curl\Curl;
use Yii;
use yii\helpers\Json;
use yii\helpers\Url;

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
            [['language_id'], 'exist', 'skipOnError' => true,
                'targetClass' => Language::class,
                'targetAttribute' => ['language_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true,
                'targetClass' => User::class,
                'targetAttribute' => ['user_id' => 'id']],
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
        return $this->hasOne(Language::class, ['id' => 'language_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    public function send($battle, bool $realSend = true): ?string
    {
        if ($battle instanceof Battle2) {
            return $this->sendSplatoon2($battle, $realSend);
        } elseif ($battle instanceof Battle) {
            return $this->sendSplatoon1($battle, $realSend);
        }
        return null;
    }

    protected function sendSplatoon1(Battle $battle, bool $realSend = true): ?string
    {
        // {{{
        $lang = $this->language->lang ?? 'en-US';
        $i18n = Yii::$app->i18n;
        $formatter = Yii::$app->formatter;
        $formatter->locale = $lang;
        $formatter->timeZone = 'Etc/UTC';

        $winlose = $i18n->translate(
            'app-slack',
            $battle->is_win === null
                ? '???'
                : ($battle->is_win ? 'won' : 'lost'),
            [],
            $lang,
        );
        $rule = $i18n->translate(
            'app-rule',
            $battle->rule->name ?? $i18n->translate('app-slack', 'unknown mode', [], $lang),
            [],
            $lang,
        );
        $stage = $i18n->translate(
            'app-map',
            $battle->map->name ?? $i18n->translate('app-slack', 'unknown stage', [], $lang),
            [],
            $lang,
        );
        $url = Url::to(['show/battle', 'screen_name' => $battle->user->screen_name, 'battle' => $battle->id], true);

        $attachment = [
            'fallback' => $i18n->translate(
                'app-slack',
                '{name}: Just {winlose} {rule} at {stage}. {url}',
                [
                    'name' => $battle->user->name,
                    'winlose' => $winlose,
                    'rule' => $rule,
                    'stage' => $stage,
                    'url' => $url,
                ],
                $lang,
            ),
            'text' => $i18n->translate(
                'app-slack',
                '{name}: Just {winlose} {rule} at {stage}. <{url}|Detail>',
                [
                    'name' => $battle->user->name,
                    'winlose' => $winlose,
                    'rule' => $rule,
                    'stage' => $stage,
                    'url' => $url,
                    'id' => $battle->id,
                ],
                $lang,
            ),
            'fields' => [
                [
                    'title' => $i18n->translate('app', 'Mode', [], $lang),
                    'value' => $rule,
                    'short' => true,
                ],
                [
                    'title' => $i18n->translate('app', 'Stage', [], $lang),
                    'value' => $stage,
                    'short' => true,
                ],
                [
                    'title' => $i18n->translate('app', 'Weapon', [], $lang),
                    'value' => $i18n->translate('app-weapon', $battle->weapon->name ?? '???', [], $lang),
                    'short' => true,
                ],
                [
                    'title' => $i18n->translate('app', 'Kill / Death', [], $lang),
                    'value' => sprintf('%s / %s', $battle->kill ?? '?', $battle->death ?? '?'),
                    'short' => true,
                ],
            ],
            'color' => $battle->is_win === null
                ? '#cccccc'
                : ($battle->is_win ? '#3969b3' : '#ec6110'),
        ];
        if ($battle->battleImageResult) {
            $attachment['image_url'] = $battle->battleImageResult->url;
        }
        return $this->doSend([
            'attachments' => [
                $attachment,
            ],
        ], $realSend);
        // }}}
    }

    protected function sendSplatoon2(Battle2 $battle, bool $realSend = true): ?string
    {
        // {{{
        $lang = $this->language->lang ?? 'en-US';
        $i18n = Yii::$app->i18n;
        $formatter = Yii::$app->formatter;
        $formatter->locale = $lang;
        $formatter->timeZone = 'Etc/UTC';

        $winlose = $i18n->translate(
            'app-slack',
            $battle->is_win === null
                ? '???'
                : ($battle->is_win ? 'won' : 'lost'),
            [],
            $lang,
        );
        $rule = $i18n->translate(
            'app-rule2',
            $battle->rule->name ?? $i18n->translate('app-slack', 'unknown mode', [], $lang),
            [],
            $lang,
        );
        $stage = $i18n->translate(
            'app-map2',
            $battle->map->name ?? $i18n->translate('app-slack', 'unknown stage', [], $lang),
            [],
            $lang,
        );
        $url = Url::to(
            ['show-v2/battle',
                'screen_name' => $battle->user->screen_name,
                'battle' => $battle->id,
            ],
            true,
        );

        $attachment = [
            'fallback' => $i18n->translate(
                'app-slack',
                '{name}: Just {winlose} {rule} at {stage}. {url}',
                [
                    'name' => $battle->user->name,
                    'winlose' => $winlose,
                    'rule' => $rule,
                    'stage' => $stage,
                    'url' => $url,
                ],
                $lang,
            ),
            'text' => $i18n->translate(
                'app-slack',
                '{name}: Just {winlose} {rule} at {stage}. <{url}|Detail>',
                [
                    'name' => $battle->user->name,
                    'winlose' => $winlose,
                    'rule' => $rule,
                    'stage' => $stage,
                    'url' => $url,
                    'id' => $battle->id,
                ],
                $lang,
            ),
            'fields' => [
                [
                    'title' => $i18n->translate('app', 'Mode', [], $lang),
                    'value' => $rule,
                    'short' => true,
                ],
                [
                    'title' => $i18n->translate('app', 'Stage', [], $lang),
                    'value' => $stage,
                    'short' => true,
                ],
                [
                    'title' => $i18n->translate('app', 'Weapon', [], $lang),
                    'value' => $i18n->translate('app-weapon2', $battle->weapon->name ?? '???', [], $lang),
                    'short' => true,
                ],
                [
                    'title' => $i18n->translate('app', 'Kill / Death', [], $lang),
                    'value' => sprintf('%s / %s', $battle->kill ?? '?', $battle->death ?? '?'),
                    'short' => true,
                ],
            ],
            'color' => $battle->is_win === null
                ? '#cccccc'
                : ($battle->is_win ? '#3969b3' : '#ec6110'),
        ];
        if ($battle->battleImageResult) {
            $attachment['image_url'] = $battle->battleImageResult->url;
        }
        return $this->doSend([
            'attachments' => [
                $attachment,
            ],
        ], $realSend);
        // }}}
    }

    public function sendTest(): bool
    {
        $lang = $this->language->lang ?? 'en-US';
        $i18n = Yii::$app->i18n;
        $formatter = Yii::$app->formatter;
        $formatter->locale = $lang;
        $formatter->timeZone = 'Etc/UTC';

        return $this->doSend([
            'text' => sprintf(
                "%s (%s)\nWebhook Test",
                $i18n->translate(
                    'app-slack',
                    'Staaaay Fresh!',
                    [],
                    $lang,
                ),
                $formatter->asDateTime(
                    $_SERVER['REQUEST_TIME'] ?? time(),
                    'long',
                ),
            ),
        ], true) !== null;
    }

    protected function buildRealQuery(array $params): array
    {
        if (!isset($params['username']) && $this->username != '') {
            $params['username'] = $this->username;
        }
        if (!isset($params['icon_emoji']) && !isset($params['icon_url']) && $this->icon != '') {
            if (strpos($this->icon, '//') === false) {
                $params['icon_emoji'] = $this->icon;
            } else {
                $params['icon_url'] = $this->icon;
            }
        }
        if (!isset($params['channel']) && $this->channel != '') {
            $params['channel'] = $this->channel;
        }
        return $params;
    }

    protected function doSend(array $params, bool $realSend): ?string
    {
        $params = Json::encode($this->buildRealQuery($params));
        if (!$realSend) {
            return $params;
        }

        $curl = new Curl();
        $curl->setUserAgent(sprintf(
            '%s/%s (+https://github.com/fetus-hina/stat.ink)',
            Yii::$app->name,
            Yii::$app->version,
        ));
        $curl->setHeader('Content-Type', 'application/json');
        $curl->post($this->webhook_url, $params);
        if ($curl->error) {
            return null;
        }
        return $params;
    }
}
