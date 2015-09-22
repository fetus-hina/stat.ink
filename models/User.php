<?php
namespace app\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "user".
 *
 * @property integer $id
 * @property string $name
 * @property string $screen_name
 * @property string $password
 * @property string $api_key
 * @property boolean $enabled
 * @property string $join_at
 */
class User extends ActiveRecord
{
    public static function find()
    {
        return parent::find()->andWhere(['[[enabled]]' => true]);
    }

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
            [['name', 'screen_name', 'password', 'api_key', 'enabled', 'join_at'], 'required'],
            [['enabled'], 'boolean'],
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
            'name' => 'Name',
            'screen_name' => 'Screen Name',
            'password' => 'Password',
            'api_key' => 'Api Key',
            'enabled' => 'Enabled',
            'join_at' => 'Join At',
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
}
