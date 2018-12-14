<?php
/**
 * @copyright Copyright (C) 2015-2018 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

declare(strict_types=1);

namespace app\models;

use Yii;
use app\components\behaviors\RemoteAddrBehavior;
use app\components\behaviors\RemoteHostBehavior;
use app\components\behaviors\RemotePortBehavior;
use app\components\behaviors\TimestampBehavior;
use app\components\behaviors\UserAgentBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

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
 *
 * @property HttpUserAgent $userAgent
 * @property LoginMethod $method
 * @property User $user
 */
class UserLoginHistory extends ActiveRecord
{
    public static function login(User $user, int $methodId): ?self
    {
        $model = Yii::createObject([
            'class' => static::class,
            'user_id' => $user->id,
            'method_id' => $methodId,
        ]);
        return $model->save() ? $model : null;
    }

    public static function tableName()
    {
        return 'user_login_history';
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::class,
            RemoteAddrBehavior::class,
            RemotePortBehavior::class,
            RemoteHostBehavior::class,
            UserAgentBehavior::class,
        ];
    }

    public function rules()
    {
        return [
            [['user_id', 'method_id'], 'required'],
            [['user_id', 'method_id', 'remote_port', 'user_agent_id'], 'default', 'value' => null],
            [['user_id', 'method_id', 'remote_port', 'user_agent_id'], 'integer'],
            [['remote_addr'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['remote_host'], 'string', 'max' => 255],
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
        ];
    }

    public function getUserAgent(): ActiveQuery
    {
        return $this->hasOne(HttpUserAgent::class, ['id' => 'user_agent_id']);
    }

    public function getMethod(): ActiveQuery
    {
        return $this->hasOne(LoginMethod::class, ['id' => 'method_id']);
    }

    public function getUser(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }
}
