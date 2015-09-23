<?php
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
    public $name;
    public $recaptcha_token;
    public $recaptcha_response;

    public function rules()
    {
        $rules = [
            [['screen_name', 'password'], 'required'],
            [['screen_name'], 'string', 'max' => 15],
            [['screen_name'], 'match',
                'pattern' => '/^[a-zA-Z0-9_]{1,15}$/',
                'message' => 'ログイン名は英数とアンダーバーの15文字以内で決めてください',
            ],
            [['screen_name'], 'unique',
                'targetClass' => User::className(),
                'message' => 'このログイン名は既に使用されています',
            ],
            [['name'], 'string', 'max' => 15],
        ];
        if (Yii::$app->params['googleRecaptcha']['siteKey'] != '') {
            $rules[] = [['recaptcha_token', 'recaptcha_response'], 'required', 'message' => 'reCAPTCHAの確認をしてください'];
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
            'screen_name'       => 'ログイン名',
            'password'          => 'パスワード',
            'name'              => '名前（表示用）',
        ];
    }

    public function toUserModel()
    {
        $u = new User();
        $u->name = $this->name == '' ? $this->screen_name : $this->name;
        $u->screen_name = $this->screen_name;
        $u->password = Password::hash($this->password);
        $u->api_key = User::generateNewApiKey();
        $u->join_at = new Now();
        return $u;
    }
}
