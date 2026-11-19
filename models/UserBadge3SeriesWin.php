<?php

/**
 * @copyright Copyright (C) 2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

namespace app\models;

use Override;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "user_badge3_series_win".
 *
 * @property integer $user_id
 * @property integer $lobby_id
 * @property integer $count
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Lobby3 $lobby
 * @property User $user
 */
class UserBadge3SeriesWin extends ActiveRecord
{
    #[Override]
    public static function tableName()
    {
        return 'user_badge3_series_win';
    }

    #[Override]
    public function rules()
    {
        return [
            [['user_id', 'lobby_id', 'count', 'created_at', 'updated_at'], 'required'],
            [['user_id', 'lobby_id', 'count'], 'default', 'value' => null],
            [['user_id', 'lobby_id', 'count'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['user_id', 'lobby_id'], 'unique', 'targetAttribute' => ['user_id', 'lobby_id']],
            [['lobby_id'], 'exist', 'skipOnError' => true, 'targetClass' => Lobby3::class, 'targetAttribute' => ['lobby_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    #[Override]
    public function attributeLabels()
    {
        return [
            'user_id' => 'User ID',
            'lobby_id' => 'Lobby ID',
            'count' => 'Count',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public function getLobby(): ActiveQuery
    {
        return $this->hasOne(Lobby3::class, ['id' => 'lobby_id']);
    }

    public function getUser(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }
}
