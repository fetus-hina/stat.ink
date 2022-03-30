<?php

/**
 * @copyright Copyright (C) 2015-2021 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\models;

use DateTimeZone;
use Throwable;
use Yii;
use app\components\helpers\DateTimeFormatter;
use app\components\helpers\Password;
use app\components\helpers\T;
use app\models\query\OstatusPubsubhubbubQuery;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Query;
use yii\helpers\Url;
use yii\web\IdentityInterface;

use const FILTER_VALIDATE_INT;
use const SORT_ASC;
use const SORT_DESC;

/**
 * This is the model class for table "user".
 *
 * @property int $id
 * @property string $name
 * @property string $screen_name
 * @property string $password
 * @property string $api_key
 * @property string $join_at
 * @property string|null $nnid
 * @property string|null $twitter
 * @property int|null $ikanakama
 * @property int|null $env_id
 * @property 'always'|'no'|'not-friend'|'not-private' $blackout
 * @property string|null $sw_friend_code
 * @property int $default_language_id
 * @property int|null $ikanakama2
 * @property int $region_id
 * @property string|null $blackout_list
 * @property int $link_mode_id
 * @property string|null $email
 * @property int|null $email_lang_id
 *
 * @property Battle[] $battles
 * @property Battle2[] $battle2s
 * @property Language|null $defaultLanguage
 * @property Environment|null $env
 * @property LoginWithTwitter|null $loginWithTwitter
 * @property OstatusRsa|null $ostatusRsa
 * @property Region|null $region
 * @property LinkMode|null $linkMode
 * @property Slack[] $slacks
 * @property UserIcon|null $userIcon
 * @property UserStat|null $userStat
 * @property UserStat2|null $userStat2
 * @property UserWeapon[] $userWeapons
 * @property UserWeapon2[] $userWeapon2s
 * @property Weapon[] $weapons
 *
 * @property-read Battle|null $latestBattle
 * @property-read Language|null $emailLang
 * @property-read Region2 $guessedSplatfest2Region
 * @property-read Weapon|null $mainWeapon
 * @property-read bool $isOstatusIntegrated
 * @property-read bool $isSlackIntegrated
 * @property-read string $iconUrl
 * @property-read string $identiconHash
 * @property-read string $jdenticonPngUrl
 * @property-read string $jdenticonUrl
 */
class User extends ActiveRecord implements IdentityInterface
{
    use openapi\Util;

    public const BLACKOUT_NOT_BLACKOUT = 'no';
    public const BLACKOUT_NOT_PRIVATE  = 'not-private';
    public const BLACKOUT_NOT_FRIEND   = 'not-friend';
    public const BLACKOUT_ALWAYS       = 'always';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user';
    }

    public static function getRoughCount(): ?int
    {
        try {
            $count = filter_var(
                (new Query())
                    ->select('[[last_value]]')
                    ->from('{{user_id_seq}}')
                    ->scalar(),
                FILTER_VALIDATE_INT
            );
            if (is_int($count)) {
                return $count;
            }
        } catch (Throwable $e) {
        }

        return null;
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
            [['default_language_id', 'region_id', 'link_mode_id'], 'required'],
            [['join_at'], 'safe'],
            [['ikanakama', 'ikanakama2', 'env_id', 'default_language_id'], 'integer'],
            [['link_mode_id', 'email_lang_id'], 'integer'],
            [['name', 'screen_name', 'twitter'], 'string', 'max' => 15],
            [['password'], 'string', 'max' => 255],
            [['email'], 'string', 'max' => 254],
            [['api_key'], 'string', 'max' => 43],
            [['nnid'], 'string', 'min' => 6, 'max' => 16],
            [['nnid'], 'match', 'pattern' => '/^[a-zA-Z0-9._-]{6,16}$/'],
            [['sw_friend_code'], 'filter',
                'filter' => function ($v) {
                    $v = trim(preg_replace('/[^\d]+/', '', (string)$v));
                    return $v != '' ? $v : null;
                },
            ],
            [['sw_friend_code'], 'string', 'min' => 12, 'max' => 12],
            [['sw_friend_code'], 'match',
                'pattern' => '/^\d{12}$/',
            ],
            [['api_key'], 'unique'],
            [['screen_name'], 'unique'],
            [['screen_name', 'twitter'], 'match', 'pattern' => '/^[a-zA-Z0-9_]{1,15}$/'],
            [['blackout'], 'default', 'value' => static::BLACKOUT_NOT_BLACKOUT],
            [['blackout_list'], 'default', 'value' => static::BLACKOUT_NOT_FRIEND],
            [['blackout', 'blackout_list'], 'in',
                'range' => [
                    static::BLACKOUT_NOT_BLACKOUT,
                    static::BLACKOUT_NOT_PRIVATE,
                    static::BLACKOUT_NOT_FRIEND,
                    static::BLACKOUT_ALWAYS,
                ],
            ],
            [['email'], 'email',
                'allowName' => false,
                'checkDNS' => true,
                'enableIDN' => false,
            ],
            [['default_language_id'], 'exist', 'skipOnError' => true,
                'targetClass' => Language::class,
                'targetAttribute' => ['default_language_id' => 'id'],
            ],
            [['region_id'], 'exist', 'skipOnError' => true,
                'targetClass' => Region::class,
                'targetAttribute' => ['region_id' => 'id'],
            ],
            [['link_mode_id'], 'exist', 'skipOnError' => true,
                'targetClass' => LinkMode::class,
                'targetAttribute' => ['link_mode_id' => 'id'],
            ],
            [['email_lang_id'], 'exist', 'skipOnError' => true,
                'targetClass' => Language::class,
                'targetAttribute' => ['email_lang_id' => 'id'],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'            => Yii::t('app', 'Internal ID'),
            'name'          => Yii::t('app', 'User Name'),
            'screen_name'   => Yii::t('app', 'Login Name'),
            'password'      => Yii::t('app', 'Password'),
            'api_key'       => Yii::t('app', 'API Token'),
            'join_at'       => Yii::t('app', 'Join At'),
            'nnid'          => Yii::t('app', 'Nintendo Network ID'),
            'sw_friend_code' => Yii::t('app', 'Friend Code (Switch)'),
            'twitter'       => Yii::t('app', 'Twitter @name'),
            'ikanakama'     => Yii::t('app', 'Ika-Nakama User ID'),
            'ikanakama2'    => Yii::t('app', 'Ika-Nakama 2 User ID'),
            'env_id'        => Yii::t('app', 'Capture Environment'),
            'blackout'      => Yii::t('app', 'Black out other players from the result image'),
            'blackout_list' => Yii::t('app', 'Black out other players on details view'),
            'default_language_id' => Yii::t('app', 'Language (used for OStatus)'),
            'region_id'     => Yii::t('app', 'Region (used for Splatfest)'),
            'link_mode_id'  => Yii::t('app', 'Link from other user\'s results'),
            'email'         => Yii::t('app', 'Email'),
            'email_lang_id' => Yii::t('app', 'Email Language'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBattles()
    {
        return $this->hasMany(Battle::class, ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBattle2s()
    {
        return $this->hasMany(Battle2::class, ['user_id' => 'id']);
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
        return $this->hasMany(Slack::class, ['user_id' => 'id']);
    }

    public function getIsSlackIntegrated(): bool
    {
        return Slack::find()
            ->andWhere([
                'suspended' => false,
                'user_id' => $this->id,
            ])
            ->exists();
    }

    public function getIsOstatusIntegrated(): bool
    {
        return T::is(OstatusPubsubhubbubQuery::class, OstatusPubsubhubbub::find())
            ->active()
            ->andWhere(['topic' => $this->id])
            ->exists();
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEnv()
    {
        return $this->hasOne(Environment::class, ['id' => 'env_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDefaultLanguage()
    {
        return $this->hasOne(Language::class, ['id' => 'default_language_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLinkMode()
    {
        return $this->hasOne(LinkMode::class, ['id' => 'link_mode_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRegion()
    {
        return $this->hasOne(Region::class, ['id' => 'region_id']);
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
        return $this->hasOne(UserStat::class, ['user_id' => 'id']);
    }

    public function getUserStat2(): ActiveQuery
    {
        return $this->hasOne(UserStat2::class, ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserWeapons()
    {
        return $this->hasMany(UserWeapon::class, ['user_id' => 'id'])
            ->with(['weapon']);
    }

    public function getUserWeapon2s(): ActiveQuery
    {
        return $this->hasMany(UserWeapon2::class, ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getWeapons()
    {
        return $this->hasMany(Weapon::class, ['id' => 'weapon_id'])->viaTable('user_weapon', ['user_id' => 'id']);
    }

    public function getMainWeapon()
    {
        return $this->hasOne(Weapon::class, ['id' => 'weapon_id'])
            ->viaTable('user_weapon', ['user_id' => 'id'], function ($query) {
                $query->orderBy('{{user_weapon}}.[[count]] DESC')->limit(1);
            });
    }

    public function getSalmonResults(): ActiveQuery
    {
        return $this->hasMany(Salmon2::class, ['user_id' => 'id'])
            ->orderBy(['id' => SORT_DESC]);
    }

    public function getLatestBattle(): ActiveQuery
    {
        return $this->hasOne(Battle::class, ['user_id' => 'id'])
            ->orderBy('{{battle}}.[[id]] DESC')
            ->limit(1);
    }

    public function getLatestBattleResultImage()
    {
        return $this
            ->hasOne(BattleImage::class, ['battle_id' => 'id'])
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

    public function getOstatusRsa()
    {
        return $this->hasOne(OstatusRsa::class, ['user_id' => 'id']);
    }

    public function getLoginHistories(): ActiveQuery
    {
        return $this->hasMany(UserLoginHistory::class, ['user_id' => 'id'])
            ->orderBy(['id' => SORT_DESC]);
    }

    public function getEmailLang(): ActiveQuery
    {
        return $this->hasOne(Language::class, ['id' => 'email_lang_id']);
    }

    public static function generateNewApiKey()
    {
        while (true) {
            $key = random_bytes(256 / 8);
            $key = rtrim(base64_encode($key), '=');
            $key = strtr($key, '+/', '_-');
            if (!self::find()->where(['[[api_key]]' => $key])->exists()) {
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
        return static::findOne(['api_key' => trim((string)$token)]);
    }

    // IdentityInterface
    public function getId()
    {
        return $this->id;
    }

    // IdentityInterface
    public function getAuthKey()
    {
        $newKeyModel = Yii::createObject([
            'class' => UserAuthKey::class,
            'user_id' => $this->id,
        ]);
        return $newKeyModel->save()
            ? $newKeyModel->auth_key_raw
            : null;
    }

    // IdentityInterface
    public function validateAuthKey($authKey)
    {
        $authKey = trim((string)$authKey);

        Yii::beginProfile($authKey, __METHOD__);
        $query = UserAuthKey::find()
            ->andWhere([
                'user_id' => $this->id,
                'auth_key_hint' => UserAuthKey::raw2hint($authKey),
            ])
            ->orderBy([
                'id' => SORT_DESC,
            ]);

        $result = false;
        foreach ($query->each() as $authModel) {
            if ($authModel->validateHash($authKey)) {
                $result = true;
                break;
            }
        }
        Yii::endProfile($authKey, __METHOD__);

        return $result;
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
            'url' => Url::to(['show-user/profile', 'screen_name' => $this->screen_name], true),
            'join_at' => DateTimeFormatter::unixTimeToJsonArray(
                strtotime($this->join_at),
                new DateTimeZone('Etc/UTC')
            ),
            'profile' => [
                'nnid'          => (string)$this->nnid !== '' ? $this->nnid : null,
                'friend_code'   => (string)$this->sw_friend_code !== ''
                    ? implode('-', [
                        'SW',
                        substr((string)$this->sw_friend_code, 0, 4),
                        substr((string)$this->sw_friend_code, 4, 4),
                        substr((string)$this->sw_friend_code, 8, 4),
                    ])
                    : null,
                'twitter'       => (string)$this->twitter != '' ? $this->twitter : null,
                'ikanakama'     => null,
                'ikanakama2'    => (string)$this->ikanakama2
                    ? sprintf('https://ikanakama.ink/users/%d', $this->ikanakama2)
                    : null,
                'environment'   => $this->env ? $this->env->text : null,
            ],
            'stat' => $this->userStat ? $this->userStat->toJsonArray() : null,
            'stats' => [
                'v1' => $this->userStat ? $this->userStat->toJsonArray() : null,
                'v2' => $this->userStat2 ? $this->userStat2->toJsonArray() : null,
            ],
        ];
    }

    public function toSalmonJsonArray(): array
    {
        static $statsCache = [];
        $stats = $statsCache[$this->id] ?? null;
        if ($stats === null) {
            $tmp = SalmonStats2::find()
                ->andWhere(['user_id' => $this->id])
                ->orderBy(['as_of' => SORT_DESC])
                ->limit(1)
                ->one();
            $stats = $tmp ? $tmp : false;
            $statsCache[$this->id] = $stats;
        }

        return [
            'id' => $this->id,
            'name' => $this->name,
            'screen_name' => $this->screen_name,
            'url' => Url::to(['show-user/profile',
                'screen_name' => $this->screen_name,
            ], true),
            'salmon_url' => Url::to(['salmon/index',
                'screen_name' => $this->screen_name,
            ], true),
            'battle_url' => Url::to(['show-v2/user',
                'screen_name' => $this->screen_name,
            ], true),
            'join_at' => DateTimeFormatter::unixTimeToJsonArray(
                strtotime($this->join_at),
                new DateTimeZone('Etc/UTC')
            ),
            'profile' => [
                'nnid'          => (string)$this->nnid !== '' ? $this->nnid : null,
                'friend_code'   => (string)$this->sw_friend_code !== ''
                    ? implode('-', [
                        'SW',
                        substr((string)$this->sw_friend_code, 0, 4),
                        substr((string)$this->sw_friend_code, 4, 4),
                        substr((string)$this->sw_friend_code, 8, 4),
                    ])
                    : null,
                'twitter'       => (string)$this->twitter != '' ? $this->twitter : null,
                'ikanakama'     => null,
                'ikanakama2'    => (string)$this->ikanakama2
                    ? sprintf('https://ikanakama.ink/users/%d', $this->ikanakama2)
                    : null,
                'environment'   => $this->env ? $this->env->text : null,
            ],
            'stats' => $stats ? $stats->toJsonArray() : null,
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

    public function getIdenticonHash(): string
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

    public function getJdenticonPngUrl(): string
    {
        return $this->getJdenticonUrl('png');
    }

    public function getJdenticonUrl(string $ext = 'svg'): string
    {
        return Url::to(
            Yii::getAlias('@jdenticon') . '/' . rawurlencode($this->identiconHash) . '.svg',
            true
        );
    }

    public function getIconUrl(string $ext = 'svg'): string
    {
        return $this->userIcon
            ? $this->userIcon->url
            : $this->getJdenticonUrl($ext);
    }

    public function getGuessedSplatfest2Region(): Region2
    {
        $regionID = (new Query())
            ->select([
                'id' => '{{region2}}.[[id]]',
            ])
            ->from('battle2')
            ->innerJoin('mode2', '{{battle2}}.[[mode_id]] = {{mode2}}.[[id]]')
            ->innerJoin('lobby2', '{{battle2}}.[[lobby_id]] = {{lobby2}}.[[id]]')
            ->innerJoin('rule2', '{{battle2}}.[[rule_id]] = {{rule2}}.[[id]]')
            ->innerJoin('splatfest2', '{{battle2}}.[[end_at]] <@ {{splatfest2}}.[[query_term]]')
            ->innerJoin('splatfest2_region', '{{splatfest2}}.[[id]] = {{splatfest2_region}}.[[fest_id]]')
            ->innerJoin('region2', '{{splatfest2_region}}.[[region_id]] = {{region2}}.[[id]]')
            ->andWhere([
                '{{battle2}}.[[user_id]]' => (int)$this->id,
                '{{mode2}}.[[key]]' => 'fest',
                '{{rule2}}.[[key]]' => 'nawabari', // It should be Turf War
                '{{lobby2}}.[[key]]' => ['standard', 'fest_normal', 'squad_4'],
            ])
            ->groupBy([
                '{{region2}}.[[id]]',
            ])
            ->orderBy([
                'COUNT(*)' => SORT_DESC,
                '{{region2}}.[[id]]' => SORT_ASC,
            ])
            ->limit(1)
            ->scalar();
        return ($regionID ? Region2::findOne(['id' => (int)$regionID]) : null)
            ?: Region2::findOne(['key' => 'jp']); // default
    }

    public static function onLogin(self $user, int $loginMethod): void
    {
        if (!$user->email) {
            return;
        }

        $mail = Yii::$app->mailer->compose(
            ['text' => '@app/views/email/login'],
            [
                'method' => LoginMethod::findOne(['id' => $loginMethod]),
                'user' => $user,
            ]
        );
        $mail->setFrom(Yii::$app->params['notifyEmail'])
            ->setTo([$user->email => $user->name])
            ->setSubject(Yii::t(
                'app-email',
                '[{site}] {name} (@{screen_name}): Logged in',
                [
                    'name' => $user->name,
                    'screen_name' => $user->screen_name,
                    'site' => Yii::$app->name,
                ],
                $user->emailLang->lang ?? 'en-US'
            ))
            ->send();
    }

    public static function openApiSchema(): array
    {
        return [];
    }

    public static function openApiDepends(): array
    {
        return [];
    }

    public static function openapiExample(): array
    {
        return [];
    }
}
