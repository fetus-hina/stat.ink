<?php

/**
 * @copyright Copyright (C) 2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\models;

use Override;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "user_password_recovery_key".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $public_id
 * @property string $secret_hash
 * @property string $created_at
 * @property string $created_ip
 * @property string $used_at
 * @property string $used_ip
 * @property string $revoked_at
 * @property integer $revoked_reason
 *
 * @property UserPasswordRecoveryKeyRevokeReason $revokedReason
 * @property User $user
 */
class UserPasswordRecoveryKey extends ActiveRecord
{
    public static function tableName()
    {
        return 'user_password_recovery_key';
    }

    #[Override]
    public function rules()
    {
        return [
            [['created_ip', 'used_at', 'used_ip', 'revoked_at', 'revoked_reason'], 'default', 'value' => null],
            [['user_id', 'public_id', 'secret_hash', 'created_at'], 'required'],
            [['user_id', 'revoked_reason'], 'default', 'value' => null],
            [['user_id', 'revoked_reason'], 'integer'],
            [['public_id', 'created_ip', 'used_ip'], 'string'],
            [['created_at', 'used_at', 'revoked_at'], 'safe'],
            [['secret_hash'], 'string', 'max' => 255],
            [['public_id'], 'unique'],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
            [['revoked_reason'], 'exist', 'skipOnError' => true, 'targetClass' => UserPasswordRecoveryKeyRevokeReason::class, 'targetAttribute' => ['revoked_reason' => 'id']],
        ];
    }

    #[Override]
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'public_id' => 'Public ID',
            'secret_hash' => 'Secret Hash',
            'created_at' => 'Created At',
            'created_ip' => 'Created Ip',
            'used_at' => 'Used At',
            'used_ip' => 'Used Ip',
            'revoked_at' => 'Revoked At',
            'revoked_reason' => 'Revoked Reason',
        ];
    }

    public function getRevokedReason(): ActiveQuery
    {
        return $this->hasOne(UserPasswordRecoveryKeyRevokeReason::class, ['id' => 'revoked_reason']);
    }

    public function getUser(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }
}
