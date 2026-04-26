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
 * This is the model class for table "user_password_recovery_key_revoke_reason".
 *
 * @property integer $id
 * @property string $key
 * @property string $name
 *
 * @property UserPasswordRecoveryKey[] $userPasswordRecoveryKeys
 */
class UserPasswordRecoveryKeyRevokeReason extends ActiveRecord
{
    public const REASON_USER_REQUEST = 1;
    public const REASON_PASSWORD_CHANGED = 2;
    public const REASON_SET_REGENERATED = 3;

    public static function tableName()
    {
        return 'user_password_recovery_key_revoke_reason';
    }

    #[Override]
    public function rules()
    {
        return [
            [['id', 'key', 'name'], 'required'],
            [['id'], 'default', 'value' => null],
            [['id'], 'integer'],
            [['key'], 'string', 'max' => 32],
            [['name'], 'string', 'max' => 64],
            [['key'], 'unique'],
            [['id'], 'unique'],
        ];
    }

    #[Override]
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'key' => 'Key',
            'name' => 'Name',
        ];
    }

    public function getUserPasswordRecoveryKeys(): ActiveQuery
    {
        return $this->hasMany(UserPasswordRecoveryKey::class, ['revoked_reason' => 'id']);
    }
}
