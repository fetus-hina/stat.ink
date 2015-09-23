<?php
namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;
use app\components\helpers\Password;

/**
 * This is the model class for table "user".
 *
 * @property integer $id
 * @property string $name
 * @property string $screen_name
 * @property string $password
 * @property string $api_key
 * @property string $join_at
 */
class User extends ActiveRecord implements IdentityInterface
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user';
    }

    public function init()
    {
        parent::init();
        $this->on(ActiveRecord::EVENT_BEFORE_VALIDATE, function ($event) {
            if ($this->api_key == '') {
                $this->api_key = self::generateNewApiKey();
            }
        });

    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'screen_name', 'password', 'api_key', 'join_at'], 'required'],
            [['join_at'], 'safe'],
            [['name'], 'string', 'max' => 10],
            [['screen_name'], 'string', 'max' => 15],
            [['password'], 'string', 'max' => 255],
            [['api_key'], 'string', 'max' => 43],
            [['api_key'], 'unique'],
            [['screen_name'], 'unique'],
            [['screen_name'], 'match', 'pattern' => '/^[a-zA-Z0-9_]{1,15}$/'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => '名前',
            'screen_name' => 'ログイン名',
            'password' => 'パスワード',
            'api_key' => 'APIキー',
            'join_at' => 'join at',
        ];
    }

    public static function generateNewApiKey()
    {
        while (true) {
            $key = random_bytes(256 / 8);
            $key = rtrim(base64_encode($key), '=');
            $key = strtr($key, '+/', '_-');
            if (self::find()->where(['[[api_key]]' => $key])->count() == 0) {
                return $key;
            }
        }
    }

    // IdentityInterface
    public static function findIdentity($id)
    {
        return static::findOne(['id' => (string)$id]);
    }

    // IdentityInterface
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne(['api_key' => (string)$token]);
    }

    // IdentityInterface
    public function getId()
    {
        return $this->id;
    }

    // IdentityInterface
    public function getAuthKey()
    {
        return null;
    }

    // IdentityInterface
    public function validateAuthKey($authKey)
    {
        return false;
    }

    public function validatePassword($password)
    {
        return Password::verify($password, $this->password);
    }

    public function rehashPasswordIfNeeded($password)
    {
        if (!Password::needsRehash($this->password)) {
            return false;
        }
        $this->password = Password::hash($password);
        return true;
    }
}
