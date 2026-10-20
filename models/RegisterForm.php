<?php

/**
 * @copyright Copyright (C) 2015-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

namespace app\models;

use Override;
use Yii;
use app\components\helpers\Password;
use app\components\helpers\db\Now;
use yii\base\Model;

class RegisterForm extends Model
{
    /**
     * Registration without password; the user signs in with the passkey created at the same time
     */
    public const SCENARIO_PASSKEY = 'passkey';

    /**
     * @var string|null
     */
    public $screen_name;
    /**
     * @var string|null
     */
    public $password;
    /**
     * @var string|null
     */
    public $password_repeat;
    /**
     * @var string|null
     */
    public $name;

    #[Override]
    public function scenarios()
    {
        return [
            self::SCENARIO_DEFAULT => ['screen_name', 'password', 'password_repeat', 'name'],
            self::SCENARIO_PASSKEY => ['screen_name', 'name'],
        ];
    }

    public function rules()
    {
        return [
            [['screen_name', 'password', 'password_repeat'], 'required'],
            [['screen_name'], 'string', 'max' => 15],
            [['screen_name'], 'match',
                'pattern' => '/^[a-zA-Z0-9_]{1,15}$/',
                'message' => Yii::t(
                    'app-user',
                    '{attribute} must be at most 15 alphanumeric or underscore characters.',
                ),
            ],
            [['screen_name'], 'unique',
                'targetClass' => User::class,
                'targetAttribute' => ['screen_name'],
                'message' => Yii::t('app-user', 'This {attribute} is already in use.'),
            ],
            [['name'], 'string', 'max' => 15],
            [['password'], 'string', 'min' => 10],
            [['password_repeat'], 'compare',
                'compareAttribute' => 'password',
                'operator' => '===',
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'screen_name' => Yii::t('app-user', 'Screen Name (Login Name)'),
            'password' => Yii::t('app', 'Password'),
            'password_repeat' => Yii::t('app-user', 'Password (again)'),
            'name' => Yii::t('app-user', 'Name (for display)'),
        ];
    }

    public function toUserModel(): User
    {
        $u = new User();
        $u->name = $this->name == '' ? $this->screen_name : $this->name;
        $u->screen_name = $this->screen_name;
        $u->password = $this->scenario === self::SCENARIO_PASSKEY
            ? null
            : Password::hash($this->password);
        $u->api_key = User::generateNewApiKey();
        $u->blackout = User::BLACKOUT_NOT_BLACKOUT;
        $u->default_language_id = $this->getCurrentLanguageId();
        $u->region_id = $this->getCurrentRegionId();
        $u->link_mode_id = $this->getDefaultLinkModeId();
        $u->join_at = new Now();
        $u->hide_data_on_toppage = false;
        return $u;
    }

    private function getCurrentLanguageId(): int
    {
        return Language::findOne(['lang' => Yii::$app->language])->id;
    }

    private function getCurrentRegionId(): int
    {
        return Region::findOne(['key' => 'jp'])->id;
    }

    private function getDefaultLinkModeId(): int
    {
        return LinkMode::findOne(['key' => 'in_game'])->id;
    }
}
