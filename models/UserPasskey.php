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
 * This is the model class for table "user_passkey".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $credential_id
 * @property string $public_key
 * @property integer $sign_count
 * @property string $aaguid
 * @property string $attestation_format
 * @property string $transports
 * @property boolean $backup_eligible
 * @property boolean $backup_state
 * @property string $nickname
 * @property string $created_at
 * @property string $updated_at
 * @property string $last_used_at
 *
 * @property UserPasskeyUser $user
 */
class UserPasskey extends ActiveRecord
{
    public static function tableName()
    {
        return 'user_passkey';
    }

    #[Override]
    public function rules()
    {
        return [
            [['last_used_at'], 'default', 'value' => null],
            [['backup_state'], 'default', 'value' => 0],
            [['user_id', 'credential_id', 'public_key', 'aaguid', 'attestation_format', 'nickname', 'created_at', 'updated_at'], 'required'],
            [['user_id', 'sign_count'], 'default', 'value' => null],
            [['user_id', 'sign_count'], 'integer'],
            [['public_key', 'aaguid', 'transports'], 'string'],
            [['backup_eligible', 'backup_state'], 'boolean'],
            [['created_at', 'updated_at', 'last_used_at'], 'safe'],
            [['credential_id'], 'string', 'max' => 1400],
            [['attestation_format'], 'string', 'max' => 32],
            [['nickname'], 'string', 'max' => 64],
            [['credential_id'], 'unique'],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => UserPasskeyUser::class, 'targetAttribute' => ['user_id' => 'user_id']],
        ];
    }

    #[Override]
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'credential_id' => 'Credential ID',
            'public_key' => 'Public Key',
            'sign_count' => 'Sign Count',
            'aaguid' => 'Aaguid',
            'attestation_format' => 'Attestation Format',
            'transports' => 'Transports',
            'backup_eligible' => 'Backup Eligible',
            'backup_state' => 'Backup State',
            'nickname' => 'Nickname',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'last_used_at' => 'Last Used At',
        ];
    }

    public function getUser(): ActiveQuery
    {
        return $this->hasOne(UserPasskeyUser::class, ['user_id' => 'user_id']);
    }
}
