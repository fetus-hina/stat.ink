<?php
/**
 * @copyright Copyright (C) 2015 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\models;

use Yii;
use yii\base\Model;
use app\components\helpers\Password;
use app\components\helpers\db\Now;
use himiklab\yii2\recaptcha\ReCaptchaValidator;

class RegisterForm extends Model
{
    public $screen_name;
    public $password;
    public $password_repeat;
    public $name;
    public $recaptcha_token;
    public $recaptcha_response;

    public function rules()
    {
        $rules = [
            [['screen_name', 'password', 'password_repeat'], 'required'],
            [['screen_name'], 'string', 'max' => 15],
            [['screen_name'], 'match',
                'pattern' => '/^[a-zA-Z0-9_]{1,15}$/',
                'message' => '{attribute} must be at most 15 alphanumeric or underscore characters.',
            ],
            [['screen_name'], 'unique',
                'targetClass' => User::className(),
                'message' => Yii::t('app', 'This {attribute} is already in use.'),
            ],
            [['name'], 'string', 'max' => 15],
            [['password_repeat'], 'compare',
                'compareAttribute' => 'password',
                'operator' => '==='],
        ];
        if (Yii::$app->params['googleRecaptcha']['siteKey'] != '') {
            $rules[] = [
                ['recaptcha_token', 'recaptcha_response'], 'required',
                    'message' => Yii::t('app', 'Please check the reCAPTCHA.')
            ];
            $rules[] = [[], ReCaptchaValidator::className(),
                'secret' => Yii::$app->params['googleRecaptcha']['secret'],
            ];
        }
        return $rules;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'screen_name'       => Yii::t('app', 'Screen Name (Login Name)'),
            'password'          => Yii::t('app', 'Password'),
            'password_repeat'   => Yii::t('app', 'Password (again)'),
            'name'              => Yii::t('app', 'Name (for display)'),
        ];
    }

    public function toUserModel()
    {
        $u = new User();
        $u->name = $this->name == '' ? $this->screen_name : $this->name;
        $u->screen_name = $this->screen_name;
        $u->password = Password::hash($this->password);
        $u->api_key = User::generateNewApiKey();
        $u->is_black_out_others = false;
        $u->join_at = new Now();
        return $u;
    }
}
