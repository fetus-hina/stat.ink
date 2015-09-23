<?php
namespace app\models;

use Yii;
use yii\base\Model;

class LoginForm extends Model
{
    public $screen_name;
    public $password;

    public function rules()
    {
        return [
            [['screen_name', 'password'], 'required'],
            [['screen_name'], 'string', 'max' => 15],
            [['screen_name'], 'match',
                'pattern' => '/^[a-zA-Z0-9_]{1,15}$/',
                'message' => 'ログイン名は英数とアンダーバーの15文字以内で入力してください。',
            ],
            [['screen_name'], 'unique',
                'targetClass' => User::className(),
                'message' => 'このログイン名は既に使用されています',
            ],
            [['password'], 'compare'],
            [['name'], 'string', 'max' => 10],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'screen_name'       => 'ログイン名',
            'password'          => 'パスワード',
            'password_repeat'   => 'パスワード（再入力）',
            'name'              => '名前（表示用）',
        ];
    }
}
