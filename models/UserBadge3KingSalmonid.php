<?php

/**
 * @copyright Copyright (C) 2023-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\models;

use Override;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "user_badge3_king_salmonid".
 *
 * @property integer $user_id
 * @property integer $king_id
 * @property integer $count
 *
 * @property SalmonKing3 $king
 * @property User $user
 */
class UserBadge3KingSalmonid extends ActiveRecord
{
    public static function tableName()
    {
        return 'user_badge3_king_salmonid';
    }

    #[Override]
    public function rules()
    {
        return [
            [['user_id', 'king_id', 'count'], 'required'],
            [['user_id', 'king_id', 'count'], 'default', 'value' => null],
            [['user_id', 'king_id', 'count'], 'integer'],
            [['user_id', 'king_id'], 'unique', 'targetAttribute' => ['user_id', 'king_id']],
            [['king_id'], 'exist', 'skipOnError' => true, 'targetClass' => SalmonKing3::class, 'targetAttribute' => ['king_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    #[Override]
    public function attributeLabels()
    {
        return [
            'user_id' => 'User ID',
            'king_id' => 'King ID',
            'count' => 'Count',
        ];
    }

    public function getKing(): ActiveQuery
    {
        return $this->hasOne(SalmonKing3::class, ['id' => 'king_id']);
    }

    public function getUser(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }
}
