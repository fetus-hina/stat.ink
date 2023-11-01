<?php

/**
 * @copyright Copyright (C) 2015-2019 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\models;

use Yii;
use app\components\helpers\Password;
use app\components\helpers\db\Now;
use yii\base\Model;

class RegisterForm extends Model
{
    public $screen_name;
    public $password;
    public $password_repeat;
    public $name;

    public function rules()
    {
        return [
            [['screen_name', 'password', 'password_repeat'], 'required'],
            [['screen_name'], 'string', 'max' => 15],
            [['screen_name'], 'match',
                'pattern' => '/^[a-zA-Z0-9_]{1,15}$/',
                'message' => '{attribute} must be at most 15 alphanumeric or underscore characters.',
            ],
            [['screen_name'], 'unique',
                'targetClass' => User::class,
                'targetAttribute' => ['screen_name'],
                'message' => Yii::t('app', 'This {attribute} is already in use.'),
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
            'screen_name' => Yii::t('app', 'Screen Name (Login Name)'),
            'password' => Yii::t('app', 'Password'),
            'password_repeat' => Yii::t('app', 'Password (again)'),
            'name' => Yii::t('app', 'Name (for display)'),
        ];
    }

    public function toUserModel()
    {
        $u = new User();
        $u->name = $this->name == '' ? $this->screen_name : $this->name;
        $u->screen_name = $this->screen_name;
        $u->password = Password::hash($this->password);
        $u->api_key = User::generateNewApiKey();
        $u->blackout = User::BLACKOUT_NOT_BLACKOUT;
        $u->default_language_id = $this->getCurrentLanguageId();
        $u->region_id = $this->getCurrentRegionId();
        $u->link_mode_id = $this->getDefaultLinkModeId();
        $u->join_at = new Now();
        return $u;
    }

    private function getCurrentLanguageId()
    {
        return Language::findOne(['lang' => Yii::$app->language])->id;
    }

    private function getCurrentRegionId()
    {
        return Region::findOne(['key' => 'jp'])->id;
    }

    private function getDefaultLinkModeId(): int
    {
        return LinkMode::findOne(['key' => 'in_game'])->id;
    }
}
