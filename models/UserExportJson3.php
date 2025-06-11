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
 * This is the model class for table "user_export_json3".
 *
 * @property integer $user_id
 * @property integer $last_battle_id
 * @property string $updated_at
 *
 * @property Battle3 $lastBattle
 * @property User $user
 */
class UserExportJson3 extends ActiveRecord
{
    public static function tableName()
    {
        return 'user_export_json3';
    }

    #[Override]
    public function rules()
    {
        return [
            [['user_id', 'last_battle_id', 'updated_at'], 'required'],
            [['user_id', 'last_battle_id'], 'default', 'value' => null],
            [['user_id', 'last_battle_id'], 'integer'],
            [['updated_at'], 'safe'],
            [['user_id'], 'unique'],
            [['last_battle_id'], 'exist', 'skipOnError' => true, 'targetClass' => Battle3::class, 'targetAttribute' => ['last_battle_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    #[Override]
    public function attributeLabels()
    {
        return [
            'user_id' => 'User ID',
            'last_battle_id' => 'Last Battle ID',
            'updated_at' => 'Updated At',
        ];
    }

    public function getLastBattle(): ActiveQuery
    {
        return $this->hasOne(Battle3::class, ['id' => 'last_battle_id']);
    }

    public function getUser(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }
}
