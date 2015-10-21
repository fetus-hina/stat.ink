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
 * @property UserStat $userStat
 * @property UserWeapon[] $userWeapons
 * @property Weapon[] $weapons
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
        return $this->hasMany(UserWeapon::className(), ['user_id' => 'id']);
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
            'join_at' => DateTimeFormatter::unixTimeToJsonArray(
                strtotime($this->join_at),
                new DateTimeZone('Etc/UTC')
            ),
        ];
    }
}
