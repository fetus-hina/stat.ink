<?php
/**
 * @copyright Copyright (C) 2015 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\models;

use DateTimeZone;
use Yii;
use app\components\helpers\DateTimeFormatter;
use app\components\helpers\Password;
use yii\db\ActiveRecord;
use yii\helpers\Url;
use yii\web\IdentityInterface;

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
 * @property integer $env_id
 * @property string $blackout
 *
 * @property Battle[] $battles
 * @property Environment $env
 * @property LoginWithTwitter $loginWithTwitter
 * @property Slack[] $slacks
 * @property UserIcon $userIcon
 * @property UserStat $userStat
 * @property UserWeapon[] $userWeapons
 * @property Weapon[] $weapons
 */
class User extends ActiveRecord implements IdentityInterface
{
    const BLACKOUT_NOT_BLACKOUT = 'no';
    const BLACKOUT_NOT_PRIVATE  = 'not-private';
    const BLACKOUT_NOT_FRIEND   = 'not-friend';
    const BLACKOUT_ALWAYS       = 'always';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user';
    }

    public static function getRoughCount()
    {
        try {
            return (new \yii\db\Query())
                ->select('[[last_value]]')
                ->from('{{user_id_seq}}')
                ->scalar();
        } catch (Exception $e) {
            return false;
        }
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
            [['ikanakama', 'env_id'], 'integer'],
            [['name', 'screen_name', 'twitter'], 'string', 'max' => 15],
            [['password'], 'string', 'max' => 255],
            [['api_key'], 'string', 'max' => 43],
            [['nnid'], 'string', 'min' => 6, 'max' => 16],
            [['nnid'], 'match', 'pattern' => '/^[a-zA-Z0-9._-]{6,16}$/'],
            [['api_key'], 'unique'],
            [['screen_name'], 'unique'],
            [['screen_name', 'twitter'], 'match', 'pattern' => '/^[a-zA-Z0-9_]{1,15}$/'],
            [['blackout'], 'default', 'value' => static::BLACKOUT_NOT_BLACKOUT],
            [['blackout'], 'in',
                'range' => [
                    static::BLACKOUT_NOT_BLACKOUT,
                    static::BLACKOUT_NOT_PRIVATE,
                    static::BLACKOUT_NOT_FRIEND,
                    static::BLACKOUT_ALWAYS,
                ],
            ],
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
            'env_id'        => Yii::t('app', 'Capture Environment'),
            'blackout'      => Yii::t('app', 'Black out other players from the result image'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBattles()
    {
        return $this->hasMany(Battle::className(), ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLoginWithTwitter()
    {
        return $this->hasOne(LoginWithTwitter::class, ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSlacks()
    {
        return $this->hasMany(Slack::className(), ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEnv()
    {
        return $this->hasOne(Environment::className(), ['id' => 'env_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserIcon()
    {
        return $this->hasOne(UserIcon::class, ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserStat()
    {
        return $this->hasOne(UserStat::className(), ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserWeapons()
    {
        return $this->hasMany(UserWeapon::className(), ['user_id' => 'id'])
            ->with(['weapon']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getWeapons()
    {
        return $this->hasMany(Weapon::className(), ['id' => 'weapon_id'])->viaTable('user_weapon', ['user_id' => 'id']);
    }

    public function getMainWeapon()
    {
        return $this->hasOne(Weapon::className(), ['id' => 'weapon_id'])
            ->viaTable('user_weapon', ['user_id' => 'id'], function ($query) {
                $query->orderBy('{{user_weapon}}.[[count]] DESC')->limit(1);
            });
    }

    public function getLatestBattle()
    {
        return $this->hasOne(Battle::className(), ['user_id' => 'id'])
            ->orderBy('{{battle}}.[[id]] DESC')
            ->limit(1);
    }

    public function getLatestBattleResultImage()
    {
        return $this
            ->hasOne(BattleImage::className(), ['battle_id' => 'id'])
            ->viaTable('battle', ['user_id' => 'id'], function ($query) {
                $query->innerJoin(
                    'battle_image',
                    'battle.id = battle_image.battle_id AND battle_image.type_id = :type',
                    [':type' => BattleImageType::ID_RESULT]
                );
                $query->orderBy('{{battle}}.[[id]] DESC');
                $query->limit(1);
            })
            ->andWhere(['battle_image.type_id' => BattleImageType::ID_RESULT]);
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
            'url' => Url::to(['show/user', 'screen_name' => $this->screen_name], true),
            'join_at' => DateTimeFormatter::unixTimeToJsonArray(
                strtotime($this->join_at),
                new DateTimeZone('Etc/UTC')
            ),
            'profile' => [
                'nnid'          => (string)$this->nnid != '' ? $this->nnid : null,
                'twitter'       => (string)$this->twitter != '' ? $this->twitter : null,
                'ikanakama'     => (string)$this->ikanakama
                    ? sprintf('http://ikazok.net/users/%d', $this->ikanakama)
                    : null,
                'environment'   => $this->env ? $this->env->text : null,
            ],
            'stat' => $this->userStat ? $this->userStat->toJsonArray() : null,
        ];
    }

    public function getUserJsonPath()
    {
        return Yii::getAlias('@app/runtime/user-json') . '/' . $this->id . '.json.gz';
    }

    public function getIsUserJsonReady()
    {
        return file_exists($this->getUserJsonPath()) && filesize($this->getUserJsonPath()) > 0;
    }

    public function getUserJsonLastUpdatedAt()
    {
        return filemtime($this->getUserJsonPath());
    }

    public function getIdenticonHash()
    {
        return substr(
            hash_hmac(
                'sha256',
                sprintf('uid=%08d', $this->id),
                Url::to(['site/index'], true)
            ),
            0,
            32
        );
    }

    public function getJdenticonPngUrl()
    {
        return Url::to(
            Yii::getAlias('@jdenticon') . '/' . rawurlencode($this->identiconHash) . '.png',
            true
        );
    }
}
