<?php

/**
 * @copyright Copyright (C) 2018-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\models;

use Yii;
use app\components\behaviors\ClientHintBehavior;
use app\components\behaviors\RemoteAddrBehavior;
use app\components\behaviors\RemoteHostBehavior;
use app\components\behaviors\RemotePortBehavior;
use app\components\behaviors\TimestampBehavior;
use app\components\behaviors\UserAgentBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Expression as DbExpr;

use function implode;
use function sprintf;

/**
 * This is the model class for table "user_login_history".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $method_id
 * @property string $remote_addr
 * @property integer $remote_port
 * @property string $remote_host
 * @property integer $user_agent_id
 * @property string $created_at
 * @property string $updated_at
 * @property integer $client_hint_id
 *
 * @property HttpClientHint $clientHint
 * @property LoginMethod $method
 * @property User $user
 * @property HttpUserAgent $userAgent
 */
class UserLoginHistory extends ActiveRecord
{
    public $remote_addr_masked;

    public static function login(User $user, int $methodId): ?self
    {
        $model = Yii::createObject([
            'class' => static::class,
            'user_id' => $user->id,
            'method_id' => $methodId,
        ]);
        return $model->save() ? $model : null;
    }

    public static function find(): ActiveQuery
    {
        $db = Yii::$app->db;

        $makeMask = function (int $ipVer, int $maskLen) use ($db): string {
            $column = $db->quoteColumnName('remote_addr');
            return sprintf(
                'WHEN %d THEN %s',
                $ipVer,
                "host(set_masklen({$column}::cidr, $maskLen))",
            );
        };
        $remoteAddrMasked = new DbExpr(sprintf(
            'CASE family(%s) %s END',
            $db->quoteColumnName('remote_addr'),
            implode(' ', [
                $makeMask(4, 24),
                $makeMask(6, 64),
                'ELSE NULL',
            ]),
        ));
        return parent::find()
            ->select([
                '{{user_login_history}}.*',
                'remote_addr_masked' => $remoteAddrMasked,
            ]);
    }

    public static function tableName()
    {
        return 'user_login_history';
    }

    public function behaviors()
    {
        return [
            ClientHintBehavior::class,
            RemoteAddrBehavior::class,
            RemoteHostBehavior::class,
            RemotePortBehavior::class,
            TimestampBehavior::class,
            UserAgentBehavior::class,
        ];
    }

    public function rules()
    {
        return [
            [['user_id', 'method_id'], 'required'],
            [['user_id', 'method_id', 'remote_port', 'user_agent_id', 'client_hint_id'], 'default',
                'value' => null,
            ],
            [['user_id', 'method_id', 'remote_port', 'user_agent_id', 'client_hint_id'], 'integer'],
            [['remote_addr'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['remote_host'], 'string', 'max' => 255],
            [['client_hint_id'], 'exist',
                'skipOnError' => true,
                'targetClass' => HttpClientHint::class,
                'targetAttribute' => ['client_hint_id' => 'id'],
            ],
            [['user_agent_id'], 'exist',
                'skipOnError' => true,
                'targetClass' => HttpUserAgent::class,
                'targetAttribute' => ['user_agent_id' => 'id'],
            ],
            [['method_id'], 'exist',
                'skipOnError' => true,
                'targetClass' => LoginMethod::class,
                'targetAttribute' => ['method_id' => 'id'],
            ],
            [['user_id'], 'exist',
                'skipOnError' => true,
                'targetClass' => User::class,
                'targetAttribute' => ['user_id' => 'id'],
            ],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'method_id' => 'Method ID',
            'remote_addr' => 'Remote Addr',
            'remote_port' => 'Remote Port',
            'remote_host' => 'Remote Host',
            'user_agent_id' => 'User Agent ID',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'client_hint_id' => 'Client Hint ID',
        ];
    }

    public function getClientHint(): ActiveQuery
    {
        return $this->hasOne(HttpClientHint::class, ['id' => 'client_hint_id']);
    }

    public function getMethod(): ActiveQuery
    {
        return $this->hasOne(LoginMethod::class, ['id' => 'method_id']);
    }

    public function getUser(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    public function getUserAgent(): ActiveQuery
    {
        return $this->hasOne(HttpUserAgent::class, ['id' => 'user_agent_id']);
    }

    public function getPseudoId(): ?string
    {
        static $counter = 0;

        if ($this->id === null) {
            return null;
        }

        return (string)(++$counter);
    }
}
