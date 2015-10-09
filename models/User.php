<?php
namespace app\models;

use DateTimeZone;
use Yii;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;
use app\components\helpers\DateTimeFormatter;
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
 * @property string $nnid
 * @property string $twitter
 * @property integer $ikanakama
 *
 * @property Battle[] $battles
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
            [['ikanakama'], 'integer'],
            [['name', 'screen_name', 'twitter'], 'string', 'max' => 15],
            [['password'], 'string', 'max' => 255],
            [['api_key'], 'string', 'max' => 43],
            [['nnid'], 'string', 'min' => 6, 'max' => 16],
            [['nnid'], 'match', 'pattern' => '/^[a-zA-Z0-9_-]{6,16}$/'],
            [['api_key'], 'unique'],
            [['screen_name'], 'unique'],
            [['screen_name', 'twitter'], 'match', 'pattern' => '/^[a-zA-Z0-9_]{1,15}$/'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'            => Yii::t('app', 'ID'),
            'name'          => Yii::t('app', 'Name'),
            'screen_name'   => Yii::t('app', 'Login Name'),
            'password'      => Yii::t('app', 'Password'),
            'api_key'       => Yii::t('app', 'API Key'),
            'join_at'       => Yii::t('app', 'Join At'),
            'nnid'          => Yii::t('app', 'Nintendo Network ID'),
            'twitter'       => Yii::t('app', 'Twitter @name'),
            'ikanakama'     => Yii::t('app', 'IKANAKAMA User ID'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBattles()
    {
        return $this->hasMany(Battle::className(), ['user_id' => 'id']);
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

    public function toJsonArray()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'screen_name' => $this->screen_name,
            'join_at' => DateTimeFormatter::unixTimeToJsonArray(
                strtotime($this->join_at),
                new DateTimeZone('Etc/UTC')
            ),
        ];
    }

    public function getSimpleStatics()
    {
        $now = isset($_SERVER['REQUEST_TIME']) ? $_SERVER['REQUEST_TIME'] : time();
        $oneDayThresholdCondition = sprintf(
            '((battle.end_at IS NOT NULL) AND (battle.end_at BETWEEN %s AND %s))',
            Yii::$app->db->quoteValue(gmdate('Y-m-d H:i:sO', $now - 86400 + 1)),
            Yii::$app->db->quoteValue(gmdate('Y-m-d H:i:sO', $now))
        );

        $query = (new \yii\db\Query())
            ->select([
                'totalBattleCount' => 'COUNT(*)',
                'totalWinRate' => sprintf(
                    '(%s * 100.0 / NULLIF(%s, 0))',
                    'SUM(CASE WHEN battle.is_win = TRUE THEN 1 ELSE 0 END)',
                    'SUM(CASE WHEN battle.is_win IS NULL THEN 0 ELSE 1 END)'
                ),
                'oneDayWinRate' => sprintf(
                    '(%s * 100.0 / NULLIF(%s, 0))',
                    "SUM(CASE WHEN {$oneDayThresholdCondition} AND battle.is_win = TRUE THEN 1 ELSE 0 END)",
                    "SUM(CASE WHEN {$oneDayThresholdCondition} AND battle.is_win IS NOT NULL THEN 1 ELSE 0 END)"
                ),
                'totalKilled' => 'SUM(CASE WHEN battle.kill IS NOT NULL AND battle.death IS NOT NULL THEN battle.kill ELSE 0 END)',
                'totalDead'   => 'SUM(CASE WHEN battle.kill IS NOT NULL AND battle.death IS NOT NULL THEN battle.death ELSE 0 END)',
                'killDeathAvailable' => 'SUM(CASE WHEN battle.kill IS NOT NULL AND battle.death IS NOT NULL THEN 1 ELSE 0 END)',
            ])
            ->from(Battle::tableName())
            ->where(['{{battle}}.[[user_id]]' => $this->id]);
        return (object)$query->createCommand()->queryOne();
    }
}
