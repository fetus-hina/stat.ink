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
 * This is the model class for table "user_badge3_boss_salmonid".
 *
 * @property integer $user_id
 * @property integer $boss_id
 * @property integer $count
 *
 * @property SalmonBoss3 $boss
 * @property User $user
 */
class UserBadge3BossSalmonid extends ActiveRecord
{
    public static function tableName()
    {
        return 'user_badge3_boss_salmonid';
    }

    #[Override]
    public function rules()
    {
        return [
            [['user_id', 'boss_id', 'count'], 'required'],
            [['user_id', 'boss_id', 'count'], 'default', 'value' => null],
            [['user_id', 'boss_id', 'count'], 'integer'],
            [['user_id', 'boss_id'], 'unique', 'targetAttribute' => ['user_id', 'boss_id']],
            [['boss_id'], 'exist', 'skipOnError' => true, 'targetClass' => SalmonBoss3::class, 'targetAttribute' => ['boss_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    #[Override]
    public function attributeLabels()
    {
        return [
            'user_id' => 'User ID',
            'boss_id' => 'Boss ID',
            'count' => 'Count',
        ];
    }

    public function getBoss(): ActiveQuery
    {
        return $this->hasOne(SalmonBoss3::class, ['id' => 'boss_id']);
    }

    public function getUser(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }
}
