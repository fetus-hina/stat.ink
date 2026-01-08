<?php

/**
 * @copyright Copyright (C) 2023-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\models;

use Override;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "user_badge3_tricolor".
 *
 * @property integer $user_id
 * @property integer $role_id
 * @property integer $count
 *
 * @property TricolorRole3 $role
 * @property User $user
 */
class UserBadge3Tricolor extends ActiveRecord
{
    public static function tableName()
    {
        return 'user_badge3_tricolor';
    }

    #[Override]
    public function rules()
    {
        return [
            [['user_id', 'role_id', 'count'], 'required'],
            [['user_id', 'role_id', 'count'], 'default', 'value' => null],
            [['user_id', 'role_id', 'count'], 'integer'],
            [['user_id', 'role_id'], 'unique', 'targetAttribute' => ['user_id', 'role_id']],
            [['role_id'], 'exist', 'skipOnError' => true, 'targetClass' => TricolorRole3::class, 'targetAttribute' => ['role_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    #[Override]
    public function attributeLabels()
    {
        return [
            'user_id' => 'User ID',
            'role_id' => 'Role ID',
            'count' => 'Count',
        ];
    }

    public function getRole(): ActiveQuery
    {
        return $this->hasOne(TricolorRole3::class, ['id' => 'role_id']);
    }

    public function getUser(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }
}
