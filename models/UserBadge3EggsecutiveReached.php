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
 * This is the model class for table "user_badge3_eggsecutive_reached".
 *
 * @property integer $user_id
 * @property integer $stage_id
 * @property integer $reached
 *
 * @property SalmonMap3 $stage
 * @property User $user
 */
class UserBadge3EggsecutiveReached extends ActiveRecord
{
    public static function tableName()
    {
        return 'user_badge3_eggsecutive_reached';
    }

    #[Override]
    public function rules()
    {
        return [
            [['user_id', 'stage_id', 'reached'], 'required'],
            [['user_id', 'stage_id', 'reached'], 'default', 'value' => null],
            [['user_id', 'stage_id', 'reached'], 'integer'],
            [['user_id', 'stage_id'], 'unique', 'targetAttribute' => ['user_id', 'stage_id']],
            [['stage_id'], 'exist', 'skipOnError' => true, 'targetClass' => SalmonMap3::class, 'targetAttribute' => ['stage_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    #[Override]
    public function attributeLabels()
    {
        return [
            'user_id' => 'User ID',
            'stage_id' => 'Stage ID',
            'reached' => 'Reached',
        ];
    }

    public function getStage(): ActiveQuery
    {
        return $this->hasOne(SalmonMap3::class, ['id' => 'stage_id']);
    }

    public function getUser(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }
}
